CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Drush Commands
 * Configuration
 * Additional Tricks
 * Maintainers


INTRODUCTION
------------

The Node Revision Cleanup module is not intended for novice users or the Drupal
layman. This module, currently, has no GUI interface. The primary reason is
because we don't want the overhead of running web server on top of the already
difficult work this module does. If it weren't for Drupal's
node_revision_delete() core function spawning all the necessary field deletion
functions, this script would probably just be a random PHP utility script you
found on some random dude's github account.

At this point, I would like to reiterate, this module is not for you if you only
have a few stray revisions laying around. There are other modules for managing
smaller numbers of revision deletions. Those modules are Node Revision Delete
https://www.drupal.org/project/node_revision_delete, Node Revision Restrict
https://www.drupal.org/project/node_revision_restrict), Revision Deletion
https://www.drupal.org/project/revision_deletion and Revisionator 3000
https://www.drupal.org/project/revisionator . However, if you have more than
500,000 rows in your node_revision table, this _may_ be the route for you.

This module is pretty straightforward: build a list of node revisions for a
given content type, break that into manageable chunks (called jobs), work on
each job sequentially until all of the revisions are cleaned up.



 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/node_revision_cleanup

 * To submit bug reports and feature suggestions, or to track changes:
   https://www.drupal.org/project/issues/search/node_revision_cleanup


REQUIREMENTS
------------

No special requirements. But you may, at your discretion, though it is highly
recommended, install and configure a Memcache server and either of the PECL
modules for your site. If you already have, kudos. The instructions for doing so
are located out there on the web somewhere. Once you've done that, install and
enable the Memcache module https://www.drupal.org/project/memcache.

If the Memcache module is installed for your site, the module will automatically
use Memcache to store its job queue. I, personally, have noticed about a 200%
bump in speed using memcache with this process, so...yeah.


INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module.
   See: https://www.drupal.org/node/895232 for further information.


DRUSH COMMANDS
--------------

 * drush node-revision-cleanup-work-list

   Iterate through listst of revision vids to delete and deletes them.

 * drush node-revision-cleanup-generate-queue

   Create work lists of revisions to be deleted for the given content type.

 * drush node-revision-cleanup-report

   Show progress report for revision deletions for the given content type.


CONFIGURATION
-------------

Don't use this on a live production site. Don't. Take proper precautions. Make
backups. Use an offline version of the site to run the process and then replace
the live database at a convenient-for-your-users time.

There. I said it. Are you happy?

 * Step zero - Back that junk up.

   No, but seriously, back up your database. Right now. Don't know how? Open a 
   terminal window. CD to your Drupal root. Type:
   drush sql-dump --result-file=/path/to/backup.sql.
   You knew how. Stop being lazy.

 * Step one - Generate the jobs.

   Again, be an awesome lamb and open your terminal window. Or five. Really,
   though, you probably only need two. In the first window, change or ssh (have
   you tried mosh http://mosh.mit.edu)? to your Drupal site's root directory.
   Run:

   drush nrc-gq %content-type% --revisions=%revisions-to-keep%

   OR

   drush nrc-gq %content-type% --tp=%time-phrase%

   Where %content-type% is the machine name of the content type for which you
   wish to delete revisions. Either provide the option --revisions with
   %revisions-to-keep%, which is the number of revisions you wish to maintain
   per node of that content type, or the option --tp with %time-phrase% being a 
   string that PHP's date_modify() function can understand like -30 days or
   -1 year. All of the node revisions _older than_ the specified %time-phrase%
   will be kept. The %time-phrase% is relative to the time the script is
   initiated.

   **WARNING:** It is entirely intentional that you are able to pass positive
   values as a %time-phrase%. The next step "work lists" will, in most cases,
   __DELETE ALL REVISIONS__ except the current if a %time-phrase% such as
   +1 week is passed into the generate queue function AND THEN the work list
   function is run. This is why they're separate - to give you time to recover
   from a boo-boo. To recover from this particular boo-boo, re-run nrc-gq with
   the right phrase to overwrite the work lists.

   This process will take between 5 seconds and 38 years, depending on how 
   tricked out your system is. Upon completion, you'll see a message along the 
   lines of:

   63 jobs available with a total of 62602 revisions to delete.

   This message means you're good to go for step two.

 * Step two - Work all the jobs!

   Read http://memegenerator.net/X-All-The-Y , you're back? Now, in the same
   terminal window where you just generated the jobs, type:

   drush nrc-wl %content-type% %group-number% &

   using the same %content-type% you entered to generate the work list.
   %group-number% should be a number to indicate which process group this is.
   When the run finishes, the number you entered for %group-number% will be
   substituted in the output message after the word 'Group'. Example:

   Group 3
   * It took 514 seconds to delete 15602 revisions (30 revisions per second).

   Now wait ... what's that ampersand doing at the end of the statement I told
   you to just drop into the shell? That ampersand tells bash to release the
   shell back to you. Why do that? So you can do something like this:

   $ drush nrc-wl blog 1 &
   $ drush nrc-wl blog 2 &
   $ drush nrc-wl blog 3 &

   Catching the drift? Running multiple instances of the script will allow you
   clean up that much faster. I've had success with up to 2 x (# of cores - 1) 
   on machines hosting the database server on the same machine. So, on my quad 
   core laptop 2 x (4 - 1) = 6. Running six instances of the script allowed the
   laptop to dedicate about 60% of each utilized core to PHP and left one core 
   for MySQL, which it hogged all of!

   As your jobs complete, they will print the number of node revisions they 
   deleted back to the screen. This may be annoying, if you intend on using that
   terminal window for something else. You can opt to pipe that information to a 
   report file, in stead by running:

   $ echo '' > /path/to/report-file.txt
   $ drush nrc-wl blog 1 >> /path/to/report-file.txt &
   $ drush nrc-wl blog 2 >> /path/to/report-file.txt &
   $ drush nrc-wl blog 3 >> /path/to/report-file.txt &

   The double greater than symbol is important, as it tells bash to append to 
   that file, rather than replace its contents. As each process finishes, it will
   append its results to the file, rather than output them to the screen.

   Alternatively, run each process in a reattachable screen or tmux session.

 * Step 3 - Monitor.

   In that second terminal window I told you to open up (you did, didn't you?)
   cd or ssh to your Drupal root and type drush nrc-report %content-type%,
   again, entering the content type you generated the job queue for. If you're
   an enterprising young lad or lass, you've got a system with watch installed.
   If you do, type watch -n2 drush nrc-report %content-type%. That particular
   switch, -n2 tells watch to re-run the script every 2 seconds. Adjust as
   needed.


ADDITIONAL TRICKS
-----------------

 * Disclaimer

   Should you perform any of the actions or utilize any of the information laid
   out in this section of this README file, you do so at your own risk. The
   author and maintainers of this module have provided this information as
   reference only, and it is up to you to do further research and due diligence
   before implementing any of these ideas.

   No, seriously, folks, don't try this stuff unless you actually know what
   you're doing.

 * Not so dangerous zone

   - Jobs

     List the background processes for the current user.

   - Disown

     This handy little command, when passed the -h option, will tell the system
     no to pass the SIGHUP signal to the processes listed in `jobs` when the
     terminal closes. Although nrc has some built in logic to safely handle
     SIGHUPs and other signals, it may be a good idea to `disown -h`, just in 
     case.

 * __DANGER ZONE__

   No, seriously, these are only to be run by pros. In non-production
   environments. Where angels with safety nets and hand grenades guard your
   backups.

   Remember that time you were told to do a backup? You backed up the server,
   too, right?

   - Nice and renice

     Running the work-lists (nrc-wl) command can take a pretty long time, 
     depending on the total number of node revisions you have to delete. Setting
     the 'niceness' of your process will help the *nix box you're working on
     understand that your process needs more or fewer resources dedicated to
     it's execution. Most operating systems operate on niceness levels between
     -20 and 20, with most processes launching with the default niceness level
     of 0. A positive nice level will tell the system to dedicate fewer
     resources, and a negative nice level tells it to dedicate more. In our
     previous example, the command to launch nrc-wl was:

     $ drush nrc-wl blog 1 >> /path/to/report-file.txt &

     To set the nice level at the time of execution to tell the system to
     dedicate more resources, you might try:

     $ nice -n -10 drush nrc-wl blog 1 >> /path/to/report-file.txt &

     If you've already launched the process and notice using top (or another 
     similar utility) that your nrc-wl isn't being taken seriously, note the PID
     number for the process and renice it, like so (Note: you'll probably have 
     to sudo this command or sudo su and issue it as root; Ubuntu 14.04, for 
     example, only allows renice by root, not under sudo.):

     $ renice -10 -p PID

     If you don't know how to find your process' PID, you should not attempt
     this.

     While you can nice and renice to -20, I try to leave that level for core
     system functionality, opting, instead, for -19.


   - Set the real time scheduling priority

     I've experienced some success with setting the scheduling priority to
     SCHED_FIFO. Rather than repeat the excellent tutorial I follow when
     adjusting scheduling priorities, I'll just 
     https://www.cyberciti.biz/faq/?p=962 you to it.

   - Set the processor affinity

     I should've stopped at the last one. Occasionally, setting a processor 
     affinity (which processor/s the script may run on) has helped. Let's say 
     you have a quad-core processor on the machine you're running nrc-wl. You 
     could, theoretically run 6 invocations of the script, assigning 1 and 2 to
     core 0, 3 and 4 to core 2, and 5 and 6 to core 3. This setup would then 
     leave the fourth core open to process all other system functions. If you've
     set the niceness and real time scheduling priority properly, you may find
     that you can max out your CPU usage on each of the 3 cores handling your 
     script. Do I suggest doing this for long periods of time? No. Are you nuts?
     Have you looked at the price of decent replacement processors recently?
     What? You rent a VPS or got the go-ahead from IT? Oh. Maybe give it a try. 
     Again, here's a link to the tutorial https://www.cyberciti.biz/tips/?p=22 .


MAINTAINERS
-----------

Current maintainers:
 * Adrian Cid Almaguer (adriancid) - https://www.drupal.org/u/adriancid
 * Jeremy Edgell (jeremyclassic) - https://www.drupal.org/u/jeremyclassic
