<?php
if(!defined('OSTSTAFFINC') || !$faq || !$thisstaff) die('Access Denied');

$category=$faq->getCategory();

?>
<h2>Frequently Asked Questions</h2>
<div id="breadcrumbs">
    <a href="kb.php">All Categories</a>
    &raquo; <a href="kb.php?cid=<?php echo $category->getId(); ?>"><?php echo $category->getName(); ?></a>
    <span class="faded">(<?php echo $category->isPublic()?'Public':'Internal'; ?>)</span>
</div>
<div style="width:700px;padding-top:2px; float:left;">
<strong style="font-size:16px;"><?php echo $faq->getQuestion() ?></strong>&nbsp;&nbsp;<span class="faded"><?php echo $faq->isPublished()?'(Published)':''; ?></span>
</div>
<div style="float:right;text-align:right;padding-top:5px;padding-right:5px;">
<?php
if($thisstaff->canManageFAQ()) {
    echo sprintf('<a href="faq.php?id=%d&a=edit" class="btn btn-default newHelpTopic">Edit FAQ</a>',
            $faq->getId());
}
?>
&nbsp;
</div>
<div class="clear"></div>
<div class="thread-body">
<?php echo $faq->getAnswerWithImages(); ?>
</div>
<div class="clear"></div>
<p>
 <div><b>Attachments:</b> <?php echo $faq->getAttachmentsLinks(); ?></div>
 <div><b>Collections:</b>
    <?php foreach ($collections=$faq->getCollections() as $collection) 
    echo sprintf('<span class="label label-default" style="background-color:%s">%s</span>', $collection['color'], $collection['name']) ?>
    </div>
</p>
<div class="faded">&nbsp;Last updated <?php echo Format::db_daydatetime($faq->getUpdateDate()); ?></div>
<hr>
<?php
if($thisstaff->canManageFAQ()) {
    //TODO: add js confirmation....
    ?>
   <div>
    <form action="faq.php?id=<?php echo  $faq->getId(); ?>" method="post" class="form-inline">
	 <?php csrf_token(); ?>
        <input type="hidden" name="id" value="<?php echo  $faq->getId(); ?>">
        <input type="hidden" name="do" value="manage-faq">
        <div>
            <strong>Options: </strong>
            <select class="form-control" name="a" style="width:200px;">
                <option value="">Select Action</option>
                <?php
                if($faq->isPublished()) { ?>
                <option value="unpublish">Unpublish FAQ</option>
                <?php
                }else{ ?>
                <option value="publish">Publish FAQ</option>
                <?php
                } ?>
                <option value="edit">Edit FAQ</option>
                <option value="delete">Delete FAQ</option>
            </select>
            <input class="btn btn-primary" type="submit" name="submit" value="Go">
        </div>
    </form>
   </div>
<?php
}
?>
