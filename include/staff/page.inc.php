<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$pageTypes = array(
        'landing' => 'Landing page',
        'offline' => 'Offline page',
        'thank-you' => 'Thank you page',
        'other' => 'Other',
        );
$info=array();
$qstr='';
if($page && $_REQUEST['a']!='add'){
    $title='Update Page';
    $action='update';
    $submit_text='Save Changes';
    $info=$page->getHashtable();
    $info['body'] = Format::viewableImages($page->getBody());
    $info['notes'] = Format::viewableImages($info['notes']);
    $slug = Format::slugify($info['name']);
    $qstr.='&id='.$page->getId();
}else {
    $title='Add New Page';
    $action='add';
    $submit_text='Add Page';
    $info['isactive']=isset($info['isactive'])?$info['isactive']:0;
    $qstr.='&a='.urlencode($_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="pages.php?<?php echo $qstr; ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2>Site Pages</h2>
 <table class="table" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr><td></td><td></td></tr> <!-- For fixed table layout -->
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em>Page information.</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180" class="required">
              Name:
            </td>
            <td class="form-group form-inline has-error">
                <input class="form-control" type="text" size="40" name="name" value="<?php echo $info['name']; ?>">
                <?php if($error['name']) echo '<span class="alert alert-danger">' .$errors['name'].'</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Type:
            </td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="type">
                    <option value="" selected="selected">Select Page Type</option>
                    <?php
                    foreach($pageTypes as $k => $v)
                        echo sprintf('<option value="%s" %s>%s</option>',
                                $k, (($info['type']==$k)?'selected="selected"':''), $v);
                    ?>
                </select>
                <?php if($error['type']) echo '<span class="alert alert-danger">' .$errors['type'].'</span>'; ?>
            </td>
        </tr>
        <?php if ($info['name'] && $info['type'] == 'other') { ?>
        <tr>
            <td width="180" class="required">
                Public URL:
            </td>
            <td><a href="<?php echo sprintf("%s/pages/%s",
                    $ost->getConfig()->getBaseUrl(), urlencode($slug));
                ?>">pages/<?php echo $slug; ?></a>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td width="180" class="required">
                Status:
            </td>
            <td class="form-group form-inline has-error">
                <input class="form-control radio" type="radio" name="isactive" value="1" <?php echo $info['isactive']?'checked="checked"':''; ?>>
                <label>Active</label>
                <input class="form-control radio" type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>>
                <label>Disabled</label>
                <?php if($error['isactive']) echo '<span class="alert alert-danger">' .$errors['isactive'].'</span>'; ?>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><b>Page body</b>: Ticket variables are only supported in thank-you pages.</em>
                <?php if($error['body']) echo '<span class="alert alert-danger">' .$errors['body'].'</span>'; ?>
            </th>
        </tr>
         <tr>
            <td colspan="2">
                <textarea class="form-control" name="body" cols="21" rows="12" style="width:98%;" class="richtext draft"
                    data-draft-namespace="page" data-draft-object-id="<?php echo $info['id']; ?>"
                    ><?php echo $info['body']; ?></textarea>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Admin Notes</strong>: Internal notes.&nbsp;</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea class="richtext no-bar" name="notes" cols="21"
                    rows="8" style="width: 80%;"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="<?php echo $submit_text; ?>">
    <input class="btn btn-warning" type="reset"  name="reset"  value="Reset">
    <input class="btn btn-danger" type="button" name="cancel" value="Cancel" onclick='window.location.href="pages.php"'>
</p>
</form>
