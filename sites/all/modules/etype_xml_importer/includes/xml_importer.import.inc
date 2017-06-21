<?php

/**
 * Create Entity
 * @param array $item
 * @param array $values
 * @param string $entity_type
 */
function etype_xml_importer_entity_create($item = array(), $values = array(), $entity_type = 'node') {

  $entity = entity_create($entity_type, $values);
  $wrapper = entity_metadata_wrapper($entity_type, $entity);
  if (count($item) > 0) {
    foreach ($item as $k => $v) {
      switch ($k) {

        case 'body':
          $wrapper->body->set(array(
            'value' => $v,
            'summary' => $item['summary'],
            'format' => 'full_html'
            )
          );
          break;

        case 'summary':
          break;

        case 'images':
          $files = array();
          foreach ($v as $arr) {
            $file = file_save_data(file_get_contents($arr['path']), 'public://' . $arr['name']);
            $file->display = '1';
            $file->description = $arr['caption'];
            $file->image_field_caption = array(
              'value' => $arr['caption'],
              'format' => 'plain_text'
            );
            $file = file_save($file);
            $file = (array) $file;
            $files[] = $file;
          }
          $wrapper->field_image->set($files);
          break;

        default:
          $wrapper->$k->set($v);
      }
    }
    $wrapper->save(TRUE);
  }
}

/**
 * Parse Olive XML Files
 */
function etype_xml_importer_olive_import() {

  global $user;
  $rand = md5(time());
  $zip_file = "/tmp/" . $rand . ".zip";
  // $import_url = 'http://test.etypegoogle4.com/xml-import/ercn_2017_01_12.zip';
  $import_url = theme_get_setting('xml_import_url');;
  $extract_dir = '/tmp/' . $rand . '/';

  /* Copy Zip file from url */
  file_put_contents($zip_file, file_get_contents($import_url));

  /* Extract Zip Archive */
  $zip = new ZipArchive;
  $res = $zip->open($zip_file);
  if ($res === TRUE) {
    $zip->extractTo($extract_dir);
    $zip->close();
  } else {
    watchdog('etype', "eType XML Importer could not open Zip Archive %file", $variables = array('%file' => $zip_file), $severity = WATCHDOG_NOTICE);
    exit;
  }

  /* Loop over directory and get the Files */
  $fileSystemIterator = new FilesystemIterator($extract_dir);
  $entries = array();
  foreach ($fileSystemIterator as $fileInfo) {
    $entry = $fileInfo->getFilename();
    if(strpos($entry, 'Section') !== FALSE) {
      $entries[] = $fileInfo->getFilename();
    }
  }

  /* Loop over found files and do the extraction */
  if (count($entries) > 0) {

    $values = array(
      'type' => 'article',
      'uid' => $user->uid,
      'status' => 1,
      'comment' => 0,
      'promote' => 0,
      'language' => LANGUAGE_NONE,
    );

    foreach ($entries as $entry) {
      $xml = file_get_contents($extract_dir . $entry);
      if ($xml !== FALSE) {
        /* parse xml in each file */
        $obj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (sizeof($obj) > 0) {
          /* loop over items in Section file */
          foreach ($obj as $stub) {
            $item = $stub->item;
            /* xml object processing of stub which contains link, title, and description */
            foreach ($item as $k => $v) {
              $array = (array) $v;
              // print_r($array);
              // title is not an object if the stub is valid
              if (!is_object($array['title'])) {
                // full article is in the linked file
                $ar_file =  $array['link'];
                $ar_xml = file_get_contents($extract_dir . $ar_file);

                /* parse article xhtml from link file */
                preg_match("/<prism:section>([^<]+)/", $ar_xml, $coincidencias);
                /* ignore classifieds */
                if ($coincidencias[1] == 'Classifieds') {
                  continue;
                }
                $array['section'] = $coincidencias[1];

                preg_match("/<dc:title>([^<]+)/", $ar_xml, $coincidencias);
                $array['title'] = $coincidencias[1];

                preg_match("/<prism:coverDate>([^<]+)/", $ar_xml, $coincidencias);
                $array['pub_date'] = $coincidencias[1];

                // s flag makes dot match linebreaks as well
                preg_match("'<body>(.*?)</body>'s", $ar_xml, $coincidencias);
                $body = $coincidencias[1];
                $body = preg_replace("'<xhtml:h1>(.*?)</xhtml:h1>'s", "", $body);
                $body = preg_replace("'<pam:media>(.*?)</pam:media>'s", "", $body);
                $body = preg_replace("'<xhtml:p prism:class=\"deck\">(.*?)</xhtml:p>'s", "", $body);
                $body = preg_replace("'<xhtml:p prism:class=\"byline\">(.*?)</xhtml:p>'s", "", $body);
                $body = preg_replace("/xhtml:([a-z]?)/", "$1", $body); // fix tags
                $array['body'] = $body;

                // get the slugline
                preg_match("'<xhtml:p prism:class=\"deck\">(.*?)</xhtml:p>'s", $ar_xml, $coincidencias);
                if (isset($coincidencias[1])) {
                  $array['slugline'] = trim(strip_tags($coincidencias[1]));
                }

                // get the byline
                preg_match("'<xhtml:p prism:class=\"byline\">(.*?)<xhtml:br />'s", $ar_xml, $coincidencias);
                if (isset($coincidencias[1])) {
                  $array['byline'] = trim(ucwords(strtolower(strip_tags($coincidencias[1]))));
                }

                // echo $ar_xml;

                /* Images */
                preg_match_all("'<pam:media>(.*?)</pam:media>'s", $ar_xml, $coincidencias);
                // loop over matches and extracat data
                if (! empty($coincidencias[1])) {
                  $matches = $coincidencias[1];
                  $images = array();
                  foreach ($matches as $item) {
                    preg_match("/<dc:format>([^<]+)/", $item, $imatches);
                    if ($imatches[1] == 'image/jpg') {
                      $iarray = array();
                      preg_match("'<pam:mediaReference pam:refid=\"(.*)\" />'", $item, $arr);
                      $iarray['image'] = $arr[1];
                      preg_match("'<pam:caption>(.*?)</pam:caption>'s", $item, $arr);
                      $iarray['caption'] = trim(strip_tags($arr[1]));
                      $images[] = $iarray;
                    }
                  }
                }

                //print_r ($images);

                if (count($images) > 0) {
                  $array['images'] = array();
                  foreach ($images as $image) {
                    $ipath = $extract_dir . 'img/' . $image['image'];
                    $array['images'][] = array(
                      'name' => $image['image'],
                      'path' => $ipath,
                      'caption' => $image['caption'],
                    );
                  }
                }
                /* Images now part of $array */
                // print_r ($array);

                $node = array(
                  'title' => $array['title'],
                  'summary' => strip_tags($array['description']),
                  'body' => $array['body'],
                  'field_kicker' => $array['slugline'],
                  'field_dateline' => $array['byline'],
                  'images' => $array['images'],
                );
                // print_r($node);

                $values['created'] = strtotime($array['pub_date']);
                etype_xml_importer_entity_create($node, $values);
                //exit;
              }
            }
          }
        }
      }
    }

  }

}

/**
 * Parse eType XML Files
 */
function etype_xml_importer_etype_import() {

  global $user;
  $rand = md5(time());
  $zip_file = "/tmp/" . $rand . ".zip";
  // $import_url = 'http://test.etypegoogle4.com/xml-import/ercn_2017_01_12.zip';
  $import_url = 'http://test.etypegoogle4.com/xml-import/xml.zip';
  $extract_dir = '/tmp/' . $rand . '/';

  /* Copy Zip file from url */
  file_put_contents($zip_file, file_get_contents($import_url));

  /* Extract Zip Archive */
  $zip = new ZipArchive;
  $res = $zip->open($zip_file);
  if ($res === TRUE) {
    $zip->extractTo($extract_dir);
    $zip->close();
  } else {
    watchdog('etype', "eType XML Importer could not open Zip Archive %file", $variables = array('%file' => $zip_file), $severity = WATCHDOG_NOTICE);
    exit;
  }

  /*
   * Loop over directory and get the Files
   * Files in xml directory in extract directory
   */

  $extract_dir = $extract_dir . '/xml/';
  $fileSystemIterator = new FilesystemIterator($extract_dir);
  $entries = array();
  foreach ($fileSystemIterator as $fileInfo) {
    $entry = $fileInfo->getFilename();
    if(strpos($entry, '.xml') !== FALSE) {
      $entries[] = $fileInfo->getFilename();
    }
  }

  /* Loop over found files and do the extraction */
  if (count($entries) > 0) {

    $values = array(
      'type' => 'article',
      'uid' => $user->uid,
      'status' => 1,
      'comment' => 0,
      'promote' => 0,
      'language' => LANGUAGE_NONE,
    );

    foreach ($entries as $entry) {
      $xml = file_get_contents($extract_dir . $entry);
      print_r($xml);
      exit;
      if ($xml !== FALSE) {
        /* parse xml in each file */
        $obj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (sizeof($obj) > 0) {

          print_r($obj);
          exit;

          /* loop over items in Section file */
          foreach ($obj as $stub) {
            $item = $stub->item;
            /* xml object processing of stub which contains link, title, and description */
            foreach ($item as $k => $v) {
              $array = (array) $v;
              // print_r($array);
              // title is not an object if the stub is valid
              if (!is_object($array['title'])) {
                // full article is in the linked file
                $ar_file =  $array['link'];
                $ar_xml = file_get_contents($extract_dir . $ar_file);

                /* parse article xhtml from link file */
                preg_match("/<prism:section>([^<]+)/", $ar_xml, $coincidencias);
                /* ignore classifieds */
                if ($coincidencias[1] == 'Classifieds') {
                  continue;
                }
                $array['section'] = $coincidencias[1];

                preg_match("/<dc:title>([^<]+)/", $ar_xml, $coincidencias);
                $array['title'] = $coincidencias[1];

                preg_match("/<prism:coverDate>([^<]+)/", $ar_xml, $coincidencias);
                $array['pub_date'] = $coincidencias[1];

                // s flag makes dot match linebreaks as well
                preg_match("'<body>(.*?)</body>'s", $ar_xml, $coincidencias);
                $body = $coincidencias[1];
                $body = preg_replace("'<xhtml:h1>(.*?)</xhtml:h1>'s", "", $body);
                $body = preg_replace("'<pam:media>(.*?)</pam:media>'s", "", $body);
                $body = preg_replace("'<xhtml:p prism:class=\"deck\">(.*?)</xhtml:p>'s", "", $body);
                $body = preg_replace("'<xhtml:p prism:class=\"byline\">(.*?)</xhtml:p>'s", "", $body);
                $body = preg_replace("/xhtml:([a-z]?)/", "$1", $body); // fix tags
                $array['body'] = $body;

                // get the slugline
                preg_match("'<xhtml:p prism:class=\"deck\">(.*?)</xhtml:p>'s", $ar_xml, $coincidencias);
                $array['slugline'] = trim(strip_tags($coincidencias[1]));

                // get the byline
                preg_match("'<xhtml:p prism:class=\"byline\">(.*?)<xhtml:br />'s", $ar_xml, $coincidencias);
                $array['byline'] = ucwords(strtolower(strip_tags($coincidencias[1])));

                // echo $ar_xml;

                /* Images */
                preg_match_all("'<pam:media>(.*?)</pam:media>'s", $ar_xml, $coincidencias);
                // loop over matches and extracat data
                if (! empty($coincidencias[1])) {
                  $matches = $coincidencias[1];
                  $images = array();
                  foreach ($matches as $item) {
                    preg_match("/<dc:format>([^<]+)/", $item, $imatches);
                    if ($imatches[1] == 'image/jpg') {
                      $iarray = array();
                      preg_match("'<pam:mediaReference pam:refid=\"(.*)\" />'", $item, $arr);
                      $iarray['image'] = $arr[1];
                      preg_match("'<pam:caption>(.*?)</pam:caption>'s", $item, $arr);
                      $iarray['caption'] = trim(strip_tags($arr[1]));
                      $images[] = $iarray;
                    }
                  }
                }

                //print_r ($images);

                if (count($images) > 0) {
                  $array['images'] = array();
                  foreach ($images as $image) {
                    $ipath = $extract_dir . 'img/' . $image['image'];
                    $array['images'][] = array(
                      'name' => $image['image'],
                      'path' => $ipath,
                      'caption' => $image['caption'],
                    );
                  }
                }
                /* Images now part of $array */
                // print_r ($array);

                $node = array(
                  'title' => $array['title'],
                  'summary' => strip_tags($array['description']),
                  'body' => $array['body'],
                  'field_kicker' => $array['slugline'],
                  'field_dateline' => $array['byline'],
                  'images' => $array['images'],
                );
                // print_r($node);

                $values['created'] = strtotime($array['pub_date']);
                etype_xml_importer_entity_create($node, $values);
                //exit;
              }
            }
          }
        }
      }
    }

  }

}

