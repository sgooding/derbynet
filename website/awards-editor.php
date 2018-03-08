<?php @session_start();
// Add, edit, reorder, and assign awards
require_once('inc/data.inc');
require_once('inc/banner.inc');
require_once('inc/authorize.inc');
require_once('inc/schema_version.inc');
require_once('inc/photo-config.inc');
require_permission(EDIT_AWARDS_PERMISSION);
?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title>Awards Editor</title>
<?php require('inc/stylesheet.inc'); ?>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.4.min.js"></script>
<script type="text/javascript" src="js/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript" src="js/dashboard-ajax.js"></script>
<script type="text/javascript" src="js/mobile-init.js"></script>
<script type="text/javascript" src="js/jquery.mobile-1.4.2.min.js"></script>
<script type="text/javascript" src="js/modal.js"></script>
<script type="text/javascript" src="js/awards-editor.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.mobile-1.4.2.css"/>
<link rel="stylesheet" type="text/css" href="css/awards-editor.css"/>
</head>
<body>
<?php make_banner('Awards Editor');

$use_subgroups = read_raceinfo_boolean('use-subgroups');

require_once('inc/awards.inc');

maybe_populate_award_types();
list($classes, $classseq, $ranks, $rankseq) = classes_and_ranks();
?>

<div class="instructions-wrapper" style="position: relative;">

<div class="left">
<p class="instructions">Drag awards to re-order.</p>
</div>

<div class="center-button-up block_buttons">
<input type="button" value="New Award" data-enhanced="true"
       onclick="handle_new_award();"/>
</div>

</div>

<div class="block_buttons">

<div class="listview">
<ul id="all_awards" data-role="listview" data-split-icon="gear">
<?php
// The list of awards gets generated by javascript code when the page loads,
// and then periodically updated as necessary.
?>
</ul>
</div><!-- listview -->

</div><!-- block_buttons -->


<div id="award_editor_modal" class="modal_dialog hidden block_buttons">
  <form id="award_editor_form">
    <input type="hidden" name="action" value="award.edit"/>
    <input type="hidden" name="awardid" value=""/>
    <label for="name">Award Name:</label>
    <input name="name" type="text"/>

    <label for="awardtype-select">Award Category:</label>
    <select name="awardtypeid" id="awardtype-select">
        <?php
        foreach ($db->query('SELECT awardtypeid, awardtype FROM AwardTypes ORDER BY awardtype') as $atype) {
          if ($atype['awardtypeid'] != AD_HOC_AWARDTYPEID) {
            echo '<option value="'.$atype['awardtypeid'].'">'
                .htmlspecialchars($atype['awardtype'], ENT_QUOTES, 'UTF-8')
                .'</option>'."\n";
          }
        }
        ?>
    </select>

    <select name="class_and_rank">
      <option selected="selected" value="0,0"><?php echo supergroup_label(); ?></option>
        <?php
        $classid = -1;
        foreach ($rankseq as $rankid) {
          $rank = $ranks[$rankid];
          if ($rank['classid'] != $classid) {
              $classid = $rank['classid'];
              echo '<option value="'.$classid.',0">'
               .htmlspecialchars($rank['class'], ENT_QUOTES, 'UTF-8')
               .'</option>'."\n";
          }
          if ($use_subgroups) {
            echo '<option value="'.$classid.','.$rank['rankid'].'">'
                 .htmlspecialchars($rank['rank'], ENT_QUOTES, 'UTF-8')
                 .'</option>'."\n";
          }
        }
        ?>
    </select>

    <br/>
    <input type="submit" data-enhanced="true"/>

    <input type="button" value="Close" data-enhanced="true"
           onclick="close_modal('#award_editor_modal');"/>
    <hr/><br/>

    <input type="button" class="delete_button" value="Delete Award"
        data-enhanced="true" onclick="handle_delete_award();"/>

  </form>
</div><!-- award_editor_modal -->

</body>
</html>

