<?php
if(!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->canEditTickets() || !$ticket) die('Access Denied');

$info=Format::htmlchars(($errors && $_POST)?$_POST:$ticket->getUpdateInfo());
if ($_POST)
    $info['duedate'] = Format::date($cfg->getDateFormat(),
       strtotime($info['duedate']));
?>
<form action="tickets.php?id=<?php echo $ticket->getId(); ?>&a=edit" method="post" id="save"  enctype="multipart/form-data">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="update">
 <input type="hidden" name="a" value="edit">
 <input type="hidden" name="id" value="<?php echo $ticket->getId(); ?>">
 <h2>Update Ticket #<?php echo $ticket->getNumber(); ?></h2>
 <table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong>Client Information</strong>: Currently selected client</em>
            </th>
        </tr>
    <?php
    if(!$info['user_id'] || !($user = User::lookup($info['user_id'])))
        $user = $ticket->getUser();
    ?>
    <tr><td>Client:</td><td>
        <div id="client-info">
            <a href="#" onclick="javascript:
                $.userLookup('ajax.php/users/<?php echo $ticket->getOwnerId(); ?>/edit',
                        function (user) {
                            $('#client-name').text(user.name);
                            $('#client-email').text(user.email);
                        });
                return false;
                "><i class="icon-user"></i>
            <span id="client-name"><?php echo Format::htmlchars($user->getName()); ?></span>
            &lt;<span id="client-email"><?php echo $user->getEmail(); ?></span>&gt;
            </a>
            <a class="action-button" style="float:none;overflow:inherit" href="#"
                onclick="javascript:
                    $.userLookup('ajax.php/tickets/<?php echo $ticket->getId(); ?>/change-user',
                            function(user) {
                                $('input#user_id').val(user.id);
                                $('#client-name').text(user.name);
                                $('#client-email').text('<'+user.email+'>');
                    });
                    return false;
                "><i class="icon-edit"></i> Change</a>
            <input type="hidden" name="user_id" id="user_id"
                value="<?php echo $info['user_id']; ?>" />
        </div>
        </td></tr>
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong>Ticket Information</strong>: Due date overrides SLA's grace period.</em>
            </th>
        </tr>
        <tr>
            <td width="160" class="required">
                Ticket Source:
            </td>
            <td class="form-group has-error form-inline">
                <select class="form-control" name="source">
                    <option value="" selected >&mdash; Select Source &mdash;</option>
                    <option value="Phone" <?php echo ($info['source']=='Phone')?'selected="selected"':''; ?>>Phone</option>
                    <option value="Email" <?php echo ($info['source']=='Email')?'selected="selected"':''; ?>>Email</option>
                    <option value="Web"   <?php echo ($info['source']=='Web')?'selected="selected"':''; ?>>Web</option>
                    <option value="API"   <?php echo ($info['source']=='API')?'selected="selected"':''; ?>>API</option>
                    <option value="Other" <?php echo ($info['source']=='Other')?'selected="selected"':''; ?>>Other</option>
                </select>
                <?php if($errors['source']) echo '<span class="alert alert-danger">'.$errors['source'].'</span>';?>
            </td>
        </tr>
        <tr>
            <td width="160">
                Collections:
            </td>
            <?php
                $sql='SELECT coll.collection_id, CONCAT_WS(" / ", pcoll.collection, coll.collection) as name, coll.color as color '
                 .' FROM '.COLLECTION_TABLE.' coll '
                  .' LEFT JOIN '.COLLECTION_TABLE.' pcoll ON(pcoll.collection_id=coll.collection_pid) ';
               if(($res=db_query($sql)) && db_num_rows($res)) { ?>
            <td class="form-group has-error form-inline">
                <?php
                $info['collections']=$ticket->getCollectionsIds();
                while(list($collectionId,$collection,$color)=db_fetch_row($res)) {
                echo sprintf('<span style="display:inline-block"><input class="form-control checkbox" type="checkbox" name="collections[]" value="%d" %s><span class="label label-default" style="background-color:%s">%s</span></span>',
                        $collectionId,
                        (($info['collections'] && in_array($collectionId,$info['collections']))?'checked="checked"':''),
                        $color,
                        $collection);
                }
                ?>
                <?php if($errors['source']) echo '<span class="alert alert-danger">'.$errors['collectionId'].'</span>';?>
            </td>
                    <?php
        } ?>
        </tr>
        <tr>
            <td width="160">
                SLA Plan:
            </td>
            <td class="form-group form-inline">
                <select class="form-control" name="slaId">
                    <option value="0" selected="selected" >&mdash; None &mdash;</option>
                    <?php
                    if($slas=SLA::getSLAs()) {
                        foreach($slas as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['slaId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                <?php if($errors['source']) echo '<span class="alert alert-danger">'.$errors['slaId'].'</span>';?>
            </td>
        </tr>
        <tr>
            <td width="160">
                Due Date:
            </td>
            <td class="form-group form-inline">
                <input class="dp form-control" id="duedate" name="duedate" value="<?php echo Format::htmlchars($info['duedate']); ?>" size="12" autocomplete=OFF>
                &nbsp;&nbsp;
                <?php
                $min=$hr=null;
                if($info['time'])
                    list($hr, $min)=explode(':', $info['time']);

                echo Misc::timeDropdown($hr, $min, 'time');
                ?>
                <?php if($errors['source']) echo '<span class="alert alert-danger">'.$errors['duedate'].'&nbsp;'.$errors['time'].'</span>';?>
                <em>Time is based on your time zone (GMT <?php echo $thisstaff->getTZoffset(); ?>)</em>
            </td>
        </tr>
        </tbody>
        <tbody id="dynamic-form">
        <?php if ($forms)
            foreach ($forms as $form) {
                $form->render(true);
        } ?>
        </tbody>
        <tbody>
        <tr>
            <th colspan="2">
                <em><strong>Internal Note</strong>: Reason for editing the ticket</em>
                <?php if($errors['source']) echo '<span class="alert alert-danger">'.$errors['note'].'</span>';?>
            </th>
        </tr>
        <tr>
            <td colspan="2" class="form-group has-error">
                <textarea class="form-control textarea" name="note" cols="21"
                    rows="6">Ticket edited by <?php echo $thisstaff->getName(); ?>.
                    </textarea>
            </td>
        </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="Save">
    <input class="btn btn-warning" type="reset"  name="reset"  value="Reset">
    <input class="btn btn-danger" type="button" name="cancel" value="Cancel" onclick='window.location.href="tickets.php?id=<?php echo $ticket->getId(); ?>"'>
</p>
</form>
<div style="display:none;" class="dialog draggable" id="user-lookup">
    <div class="body"></div>
</div>
