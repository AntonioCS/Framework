<table<?php echo $this->attributes ?>>
<?php if ($this->caption) { ?>
    <caption><?php echo $this->caption ?></caption> 
<?php } ?>

<?php if ($this->thead) { ?>
  <thead>
    <?php echo $this->thead ?>
  </thead>
<?php } ?>

<?php if ($this->tfoot) { ?>
  <tfoot>
    <?php echo $this->tfoot ?>
  </tfoot>
<?php } ?>
  <tbody>
   <?php echo $this->elements ?> 
  </tbody>
</table>