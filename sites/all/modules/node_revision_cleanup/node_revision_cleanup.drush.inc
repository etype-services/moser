<?php

/**
 * @file
 * Drush integration for node_revision_cleanup.
 */

/**
 * Implements COMMANDFILE_drush_command().
 */
function node_revision_cleanup_drush_command() {
  $items = array();

  $items['node-revision-cleanup-work-list'] = array(
    'description' => dt('Iterate through listst of revision vids to delete and deletes them.'),
    'arguments' => array(
      'type' => dt('Machine name of content type list to work.'),
      'group' => dt('Group ID number.'),
    ),
    'required-arguments' => TRUE,
    'examples' => array(
      'drush node-revision-cleanup-work-list blog 1' => dt('Runs jobs to delete blog node revisions as group 1.'),
      'drush nrc-wl article 4' => dt('Runs jobs to delete articl node revisions as group 4.'),
    ),
    'aliases' => array('nrc-wl'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_FULL,
  );
  $items['node-revision-cleanup-generate-queue'] = array(
    'description' => dt('Create work lists of revisions to be deleted for the given content type.'),
    'arguments' => array(
      'type' => dt('Machine name of content type.'),
    ),
    'options' => array(
      'revisions' => dt('The number of revisions per node of the provided type to keep.'),
      'tp' => dt('The number of revisions per node of the provided type to keep, based on revision age. Provide a time phrase that is parseable by date_modify(). Eg "-60 days" or "-1 year". All times are relative to the time the command runs. A positive number will probably DELETE ALL REVISIONS except the current. I wouldn\'t suggest doing that.'),
    ),
    'required-arguments' => 1,
    'examples' => array(
      'drush nrc-gq article --revisions=10' => dt('Generate work lists for article nodes, excluding the 10 most recent revisions.'),
      'drush nrc-gq article --tp="-30 days"' => dt('Generate work lists for article nodes, excluding revisions from the last 30 days.'),
    ),
    'aliases' => array('nrc-gq'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_FULL,
  );
  $items['node-revision-cleanup-report'] = array(
    'description' => dt('Show progress report for revision deletions for the given content type.'),
    'arguments' => array(
      'type' => dt('Machine name of content type list to work.'),
    ),
    'required-arguments' => TRUE,
    'examples' => array(
      'drush node-revision-cleanup-report blog' => dt('Show the progress for revision deletions for blog node.'),
      'drush nrc-report article' => dt('Show the progress for revision deletions for article nodes.'),
    ),
    'aliases' => array('nrc-report'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_FULL,
  );

  return $items;
}

/**
 * Implements hook_drush_help().
 */
function node_revision_cleanup_drush_help($section) {
  switch ($section) {
    case 'meta:node_revision_cleanup:title':
      return dt('Node Revision Cleanup commands');

    case 'meta:node_revision_cleanup:summary':
      return dt("Interacts with the Node Revision Cleanup module's functionalities.");

    case 'drush:node-revision-cleanup-work-list':
      return dt('Iterate through listst of revision vids to delete and deletes them.');

    case 'drush:node-revision-cleanup-generate-queue':
      return dt('Create work lists of revisions to be deleted for the given content type.');

    case 'drush:node-revision-cleanup-report':
      return dt('Show progress report for revision deletions for the given content type.');
  }
}

/**
 * Iterate through the lists of vids to delete and delete them.
 *
 * @param string $content_type
 *   Machine name of the content type.
 * @param int $group
 *   ID of the process group.
 */
function drush_node_revision_cleanup_work_list($content_type, $group) {
  $v = new NodeRevisionCleanupVariableCRUD();

  if ($v->vget('node_revision_cleanup_' . $content_type . '_lock', 0) == 1) {
    return drush_set_error('NRC_LOCKED', dt('The job list has not finished generating or the job list generation has hung. Wait for the process to finish.'));
  }

  // A place to initialize empty iterators and containers.
  $revision_count = 0;
  $counter = 0;
  $job = array();

  $processes = (int) $v->vget('node_revision_cleanup_' . $content_type . '_processes_running', 0);
  $v->vset('node_revision_cleanup_' . $content_type . '_processes_running', $processes + 1);
  if (!(bool) $v->vget('node_revision_cleanup_' . $content_type . '_begin', 0)) {
    // We're the first process in. Set the "begin time" variable.
    $v->vset('node_revision_cleanup_' . $content_type . '_begin', time());
  }

  $start_time = time();

  // Do not assign to a variable; we want to make sure we check the latest
  // value on each go 'round.
  while (count($v->vget('node_revision_cleanup_' . $content_type . '_jobs_available', array())) > 0) {
    // Get a job from the available jobs queue.
    $jobs_available = $v->vget('node_revision_cleanup_' . $content_type . '_jobs_available', array());
    if (is_array($jobs_available) && count($jobs_available) > 0) {
      // Get and remove the job id from the available jobs queue.
      $job_id = array_shift($jobs_available);
      $v->vset('node_revision_cleanup_' . $content_type . '_jobs_available', $jobs_available);
      // Pull down the job itself.
      $job = $v->vget('node_revision_cleanup_' . $content_type . '_job_number_' . $job_id, NULL);
      if (is_array($job)) {
        // Remove the job.
        $v->vdel('node_revision_cleanup_' . $content_type . '_job_number_' . $job_id);
        // Keep a per-job counter in case a kill signal is received.
        $counter = 0;
        // Finally! Let's do some work.
        foreach ($job as $revision) {
          if (node_revision_delete($revision)) {
            ++$revision_count;
          }
          ++$counter;
        }
        $progress = $v->vget('node_revision_cleanup_' . $content_type . '_completed', 0);
        $v->vset('node_revision_cleanup_' . $content_type . '_completed', $progress + count($job));
      }
    }
  }

  // Gather reporting metrics.
  $end_time = time();
  $elapsed_time = $end_time - $start_time;

  // Shut down.
  $processes = (int) $v->vget('node_revision_cleanup_' . $content_type . '_processes_running', 0);
  $v->vset('node_revision_cleanup_' . $content_type . '_processes_running', $processes - 1);

  if ($revision_count > 1) {
    // Make sure nothing's gone wrong with timekeeping.
    $elapsed_time == 0 ? $elapsed_time = 1 : $elapsed_time = $elapsed_time;
    // Print out report.
    $message = sprintf("###" . dt('Group') . "%d" . PHP_EOL, $group);
    $message .= sprintf("* It took %d seconds to delete %d revisions (%d revisions per second).", $elapsed_time, $revision_count, $revision_count / $elapsed_time);
    drush_log($message, 'ok');
  }
  else {
    $message = dt('No revisions deleted. Run nrc-gq before nrc-wl to ensure you have a log of versions to delete.');
    drush_log($message, 'failed');
  }

  // Clean up cruft from $v->vset().
  if ($v->vget('node_revision_cleanup_' . $content_type . '_processes_running', 0) == 0) {
    node_revision_cleanup_cruft($content_type);
  }
}

/**
 * Generate queue and jobs.
 *
 * @param string $content_type
 *   The machine name of the content type for which to generate a job queue.
 */
function drush_node_revision_cleanup_generate_queue($content_type) {

  $process_per_loop = 1000;
  $count_of_revisions_to_keep = 0;
  $max_revisions_statement = NULL;
  $date_period_statement = NULL;
  $placeholders = array(':content_type' => $content_type);
  $count_of_revisions_to_keep = drush_get_option('revisions', FALSE);
  $time_phrase = drush_get_option('tp', FALSE);
  if ($count_of_revisions_to_keep == FALSE && $time_phrase == FALSE) {
    return drush_set_error('NO_OPTS', dt('Either a revision count (--revisions=X) or a time phrase (--tp="-X time") must be provided.'));
  }
  if (!empty($count_of_revisions_to_keep) && !empty($time_phrase)) {
    return drush_set_error('TOO_MANY_OPTS', dt("I don't know what you want from me! You may only provide a value for EITHER --revisions OR --tp, not both."));
  }
  switch ($time_phrase) {
    case FALSE:
      $max_revisions_statement = 'HAVING COUNT(r.vid) > :max_revisions';
      $placeholders[':max_revisions'] = $count_of_revisions_to_keep;

      break;

    default:
      $date = date_create();
      date_modify($date, $time_phrase);
      $date_period_statement = 'AND r.timestamp < ' . $date->format('U');

      break;
  }

  $v = new NodeRevisionCleanupVariableCRUD();

  // Clean up leftovers from previous runs.
  node_revision_cleanup_cruft($content_type);

  // Lock so that the cleanup process doesn't start while this is ongoing.
  $v->vset('node_revision_cleanup_' . $content_type . '_lock', 1);

  // A place to initialize iterators and containers.
  $count_of_revisions_to_delete = 0;
  $vids_to_delete = array();
  $new_vids_to_delete = array();
  $content_nids = array();

  // Let's get down to business! Select all of the revision vids with their nid,
  // if the nid is of the proper type and has more than the given revisions.
  // This is a combination of the query found in node_revision_list() in core
  // and Node Revision Delete (as of 7.x-3.x-dev) module's
  // _node_revision_delete_do_delete(). I've done nothing but jam them together
  // and carve off the extra bits. Kinda like a turducken.
  $query = "SELECT r.vid, r.nid
            FROM {node_revision} r
            /* Make sure we get all related nodes & revisions. */
            LEFT JOIN {node} n
              ON n.nid = r.nid
            WHERE r.nid IN (
               SELECT n.nid
               FROM {node} n
               INNER JOIN {node_revision} r
                ON r.nid = n.nid
               WHERE n.type = :content_type
               GROUP BY r.nid
               $max_revisions_statement
            )
            $date_period_statement
            /* And protect the user from deleting the current revision. */
            AND n.vid <> r.vid
            ORDER BY r.timestamp DESC";
  $result = db_query($query, $placeholders);
  foreach ($result as $record) {
    $content_nids[$record->nid][] = $record->vid;
  }
  // Process the array of nids to reduce each nid to only contain the vids that
  // need to be deleted.
  foreach ($content_nids as $nid) {
    $vids_to_delete[] = array_slice($nid, $count_of_revisions_to_keep, count($nid));
  }
  // Clean up.
  $content_nids = array();
  unset($content_nids);
  // Reprocess the multidimensional array into just a list of vids to delete.
  foreach ($vids_to_delete as $vids) {
    if (is_array($vids)) {
      $count_of_revisions_to_delete += count($vids);
      foreach ($vids as $vid) {
        $new_vids_to_delete[] = $vid;
      }
    }
    else {
      ++$count_of_revisions_to_delete;
      $new_vids_to_delete[] = $vids;
    }
  }
  // Clean up.
  $vids_to_delete = array();
  unset($vids_to_delete);
  // Sort the list of vids to be oldest to newest.
  asort($new_vids_to_delete);
  // Set a variable so we can track progress.
  $v->vset('node_revision_cleanup_' . $content_type . '_total_vids', $count_of_revisions_to_delete);
  $v->vset('node_revision_cleanup_' . $content_type . '_completed', 0);
  // Build the queue.
  $queue = array_chunk($new_vids_to_delete, $process_per_loop);
  $queue_length = count($queue);
  for ($i = 0; $i < $queue_length; $i++) {
    $jobs_available[] = $i;
    $v->vset('node_revision_cleanup_' . $content_type . '_job_number_' . $i, $queue[$i]);
  }
  // Post the available jobs listing.
  $v->vset('node_revision_cleanup_' . $content_type . '_jobs_available', $jobs_available);

  // Unlock so other processes can move forward.
  $v->vset('node_revision_cleanup_' . $content_type . '_lock', 0);

  // Inform the user that the queue has been created.
  $message = sprintf("%d jobs available with a total of %d revisions to delete.", count($jobs_available), $count_of_revisions_to_delete);
  drush_log($message, 'ok');
}

/**
 * Report on ongoing revision deletions.
 *
 * @param string $content_type
 *   The content type job queue on which to report.
 */
function drush_node_revision_cleanup_report($content_type) {
  $v = new NodeRevisionCleanupVariableCRUD();
  $processes = $v->vget('node_revision_cleanup_' . $content_type . '_processes_running', 0);
  if ($processes > 0) {
    $queue = $v->vget('node_revision_cleanup_' . $content_type . '_jobs_available', array());
    $jobs_available = count($queue);
    $vids_to_delete = 0;
    for ($i = 0; $i < $jobs_available; $i++) {
      $vids_to_delete += count($v->vget('node_revision_cleanup_' . $content_type . '_job_number_' . $queue[$i], 0));
    }
    $start = $v->vget('node_revision_cleanup_' . $content_type . '_begin', time());
    $now = time();
    $elapsed = _node_revision_cleanup_seconds_to_time($now - $start);
    $progress = $v->vget('node_revision_cleanup_' . $content_type . '_completed', 0);
    $total = $v->vget('node_revision_cleanup_' . $content_type . '_total_vids', 0);
    $time_gone = '';
    foreach ($elapsed as $key => $value) {
      if ($value > 0) {
        $time_gone .= $value . " " . $key . " ";
      }
    }
    // Print out findings.
    $message = sprintf("== NODE REVISION DELETION STATS FOR CONTENT TYPE %s ==" . PHP_EOL, strtoupper($content_type));
    $message .= sprintf("%d percent complete." . PHP_EOL, ($progress / $total) * 100);
    $message .= sprintf("%d processes running." . PHP_EOL, $processes);
    $message .= sprintf("%d deletions per second (approximate)." . PHP_EOL, $progress / ($now - $start));
    $message .= sprintf("%d revisions have been deleted so far." . PHP_EOL, $progress);
    $message .= sprintf("There are %d jobs remaining with %d total revisions to be deleted." . PHP_EOL, $jobs_available, $vids_to_delete);
    $message .= sprintf("%shave elapsed since the first deletion process started." . PHP_EOL, $time_gone);
    $message .= sprintf("== END REPORT FOR CONTENT TYPE %s ==" . PHP_EOL, strtoupper($content_type));
    drush_print($message);
  }
  else {
    drush_log(dt('No processes running.'), 'failed');
  }
}
