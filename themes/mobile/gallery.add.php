<?php


?>
<div id="addpod">
    <form class="valid" action="<? $POD->siteRoot(false)."/submit_pod"; ?>" method="post" id="post_something"  enctype="multipart/form-data">

        <input type="radio" name="f_type" value="pod" checked="checked"/>&nbsp; Pod or,<br />
        <input type="radio" name="f_type" value="theme"/>&nbsp; Theme<br /><br />

        <label for="pod_name">Name your <span class="f_type">POD</span></label><br />
        <input name="pod_name" type="text" required/><br /><br />

        <label for="description">Describe your <span class="f_type">POD</span></label><br />
        <textarea name="description" rows="2" cols="40" ></textarea><br /><br />

        <label for="tags">Add tags to your <span class="f_type">POD</span></label><br />
        <input name="tags" id="tags" value="" /><br /><br />

        <label for="upload">Upload <span class="f_type">Pod</span>: </label>as a zip or tar file<br />
	<input name="upload" type="file" id="pod_file" value="upload" /><br /><br />

        <input type="submit" name="submit" id="save_pod" />
    </form>
</div>

<script type="text/javascript">

            $(document).ready(function(){

                $('#tags').tagsInput();

                $(':radio').change(function(){
                    var type= $(this).val();
                    $('.f_type').html(type);
                });
            });

</script>