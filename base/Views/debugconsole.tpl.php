<script type="text/javascript">
(function () { 
    var debug_msgs = new Array(<?php echo $this->debug_msg ?>);
    if (debug_msgs.length == 0)
        return;
    try {
        for (var i = 0,n = debug_msgs.length;i<n;i++) {            
            console.debug('AcsFramework ' + debug_msgs[i]);
        }    
    }catch(e) {}
})();
</script>