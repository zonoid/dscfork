<?php
// TODO: J4/5 Review for Joomla 4/5 API/structure compatibility (e.g., controllers, models, views, JHtml, JRoute, JForm, JText, database queries, jimport vs use).
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_( 'stylesheet', 'menu.css', 'media/com_sample/css/' );
?>
<div id="<?php echo $this->name; ?>" class="submenu">
	
<?php foreach($this->items as $item):?>
	
	<?php if($this->hide):?>
		
		<?php if($item[2] == 1):?>
			<span class="nolink active"><?php echo $item[ 0 ]; ?></span>
		<?php else: ?>
			<span class="nolink"><?php echo $item[ 0 ]; ?></span>
		<?php endif; ?>
		
	<?php else: ?>
		
		<?php if($item[2] == 1):?>
			<a class="active" href="<?php echo $item[ 1 ]; ?>"><?php echo $item[ 0 ]; ?></a>
		<?php else: ?>
			<a href="<?php echo $item[ 1 ]; ?>"><?php echo $item[ 0 ]; ?></a>
		<?php endif; ?>
		
	<?php endif; ?>

<?php endforeach; ?>

</div>