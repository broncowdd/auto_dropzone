<?php
/* Auto_dropzone.php v1.0 
    author: Bronco
    email: bronco@warriordudimanche.net
    web: http://warriordudimanche.net
    licence: free & free ^^ (feel free to use & modify for free)

    based on http://www.script-tutorials.com/html5-drag-and-drop-multiple-file-uploader/ 

	todo:
    ----------------------------------------------
	* fallback for old browsers ?
	* selective uploads paths (depending on mime): if destination_filepath is an array(mime=>path) ->  adapt behaviour.


    How to use it ?
    ----------------------------------------------
    just include this file in your project: the script generates the dropzone and handles the upload.
    If you need, you can configure it like explained below

    configuration / init
    ----------------------------------------------
    you can configure outside this script, before the include('auto_dropzone.php');
    with this kind of init:

  $auto_dropzone=array(
    'destination_filepath'=>'path/to/', 
    'dropzone_text'=>'D&D here !',
    'dropzone_id'=>'drop_images', 
    'dropzone_class'=>'drop_images', // no .
    'dropzone_style'=>'',
    'info_style'=>'',
    'allowed_filetypes'=>'png,gif,jpg,jpeg',
    'use_style'=>true, // false if you're using an css file
  );
  
    'destination_filepath' key:'destination_filepath'=>"upload_path/" (with ending slash)
    if not specified, the destination folder will be destination/ (created on the first start)
    you also can set specific paths for each mime type like that
        'destination_filepath'=>array('gif'=>'path/to/gif/','png'=>'path/to/png/' ... )

    'allowed_filetypes' key: restrict allowed filetypes (separated with ,)
    ----------------------------------------------

* this is the default config
*/

if (empty($auto_dropzone['allowed_filetypes'])){   $auto_dropzone['allowed_filetypes']='png,gif';}
if (!isset($auto_dropzone['use_style'])){          $auto_dropzone['use_style']=true;}// put false if you're using an external css file
if (empty($auto_dropzone['max_length'])){          $auto_dropzone['max_length']=512;}// Mo 
if (empty($auto_dropzone['dropzone_text'])){       $auto_dropzone['dropzone_text']='Drop files here'; }
if (empty($auto_dropzone['dropzone_id'])){         $auto_dropzone['dropzone_id']='dropArea'; }
if (empty($auto_dropzone['dropzone_class'])){      $auto_dropzone['dropzone_class']='dropArea'; }
if (empty($auto_dropzone['dropzone_style'])){      $auto_dropzone['dropzone_style']=''; }else{$auto_dropzone['dropzone_style']=' style="'.$auto_dropzone['dropzone_style'].'" ';}
if (empty($auto_dropzone['info_style'])){          $auto_dropzone['info_style']=''; }else{$auto_dropzone['info_style']=' style="'.$auto_dropzone['info_style'].'" ';}
if (empty($auto_dropzone['destination_filepath'])){$auto_dropzone['destination_filepath']='destination/'; }

if (!is_array($auto_dropzone['destination_filepath'])&&!is_dir($auto_dropzone['destination_filepath'])){      mkdir($auto_dropzone['destination_filepath'],01777);file_put_contents($auto_dropzone['destination_filepath'].'index.html','');}



if ($_FILES){ 
    // HANDLE UPLOAD
    function bytesToSize1024($bytes, $precision = 2) {
        $unit = array('B','KB','MB');
        return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision).' '.$unit[$i];
    }

    if (isset($_FILES['myfile'])) {
        $sFileName = $_FILES['myfile']['name'];
        $sFileType = $_FILES['myfile']['type'];
        $sFileSize = bytesToSize1024($_FILES['myfile']['size'], 1);
        $sFileExt  = pathinfo($sFileName,PATHINFO_EXTENSION);

        $ok='<li class="DD_file DD_success '.$sFileExt.'">   
            <span class="DD_filename">'.$sFileName.'</span>
            [<em class="DD_filetype">'.$sFileType.'</em>, 
            <em class="DD_filesize">'.$sFileSize.'</em>] [OK]
        </li>';
        $notok='<li class="DD_file DD_error">   
            <span class="DD_filename">'.$sFileName.'</span>
            [<em class="DD_filetype">'.$sFileType.'</em>, 
            <em class="DD_filesize">'.$sFileSize.'</em>] [UPLOAD ERROR !]
        </li>';
        if (
            is_array($auto_dropzone['destination_filepath'])
            &&!empty($auto_dropzone['destination_filepath'][$sFileExt])
            &&is_dir($auto_dropzone['destination_filepath'][$sFileExt])            
        ){
            $sFileName = $auto_dropzone['destination_filepath'][$sFileExt].$sFileName;
            echo $ok;
            rename($_FILES['myfile']['tmp_name'], $sFileName );
        }elseif(
            is_array($auto_dropzone['destination_filepath'])
            &&!empty($auto_dropzone['destination_filepath'][$sFileExt])
            &&!is_dir($auto_dropzone['destination_filepath'][$sFileExt])
            ||
            is_string($auto_dropzone['destination_filepath'])
            &&!is_dir($auto_dropzone['destination_filepath'])
        ){
            //dir error
            echo '<li class="DD_file DD_error"><span class="DD_filename">Upload path problem  with '.$sFileName.' </span></li>';
        }elseif(is_dir($auto_dropzone['destination_filepath'])){
            $sFileName = $auto_dropzone['destination_filepath'].$sFileName;
            echo $ok;
            rename($_FILES['myfile']['tmp_name'], $sFileName );
        }    
    } else {
        echo $notok;
    }
}else{
    // GENERATE DROPZONE
    if ($auto_dropzone['use_style']){
        echo '
        <style>
            .DD_dropzone{
				font-family:courier;cursor:pointer;
				text-shadow:0 2px 1px white;
				box-sizing: border-box;
				text-align:center;
                box-shadow:inset 0 2px 3px;
				margin:5px;padding:20px;
				width:100%;min-height:100px;
				border-radius:3px;border:3px dashed darkblue;
				background-color:#99F;
			}
            .DD_uploading{ background-color:orange;}
            .DD_hover{background-color:yellow;box-shadow:inset 0 4px 8px;}
			.DD_text{font-size:30px;margin:15px 0;font-weight:bold;text-shadow:0 2px 2px white;}
            .DD_file,.DD_error{padding:10px;box-sizing: border-box;border-radius:3px;box-shadow:0 1px 2px #0A0;display:block;margin-bottom:5px;}
            .DD_success{background-color:#0F0;}
            .DD_error{font-weight:bold;background-color:#F00;color:white;box-shadow:0 1px 2px #F00;text-shadow: 0 1px 1px maroon}
            .DD_info{font-size:12px;text-align:left;}
            .DD_info li.DD_file{list-style:none;}
            #DD_progressbar{
				overflow:hidden;
				font-size:12px;
				box-sizing: border-box;
				border-radius:3px;
				padding:3px 0;
				text-align:center;
				background-color:#3f3;
				box-shadow:0 0 3px #0F0;
				height:20px;width:0%
			}
        </style>
        ';
    }
?>

        <div class="<?php echo $auto_dropzone['dropzone_class']; ?> DD_dropzone" id="<?php echo $auto_dropzone['dropzone_id'];?>" <?php echo $auto_dropzone['dropzone_style'];?>>
            <p class="DD_text"><?php echo $auto_dropzone['dropzone_text']; ?></p>

            <div class="DD_info" <?php echo $auto_dropzone['info_style'];?>>
                <div id="result"></div>
                <div id="DD_progressbar"></div>
            </div>
        </div>


    <script>
        
        // variables
        var dropArea        = document.getElementById('<?php echo $auto_dropzone['dropzone_id'];?>');
        var bar             = document.getElementById('DD_progressbar');
        var result          = document.getElementById('result');
        var list            = [];
        var totalSize       = 0;
        var totalProgress   = 0;

        function filetype(filemime){
            var parts = filemime.split("/");
            return (parts[(parts.length-1)]);
        }
        function is_allowed(filemime){
			var r='<?php echo $auto_dropzone['allowed_filetypes']; ?>';
			m=filetype(filemime);if (m==''){return false;}
			if(r.indexOf(m)>-1||r==''){return true; }
			else{return false;}
		}


        // main initialization
        (function(){

            // init handlers
            function initHandlers() {
                dropArea.addEventListener('drop', handleDrop, false);
                dropArea.addEventListener('dragover', handleDragOver, false);
                dropArea.addEventListener('dragleave', handleDragLeave, true );
            }

            // draw progress
            function drawProgress(progress) {
                if(progress!='NaN'){
                    percent=Math.floor(progress*100)+'%';
                    bar.style.width=percent;
                    bar.innerHTML=percent;
                }else{bar.style.width='0';}
            }

            // drag over
            function handleDragOver(event) {
                event.stopPropagation();
                event.preventDefault();
                dropArea.className = 'DD_dropzone DD_hover';
            }

            // drag leave
            function handleDragLeave(event) {
                event.stopPropagation();
                event.preventDefault();
                dropArea.className = 'DD_dropzone';
            }

            // drag drop
            function handleDrop(event) {
                event.stopPropagation();
                event.preventDefault();
                processFiles(event.dataTransfer.files);
            }

            // process bunch of files
            function processFiles(filelist) {
                if (!filelist || !filelist.length || list.length) return;
                totalSize = 0;
                totalProgress = 0;
                result.textContent = '';

                for (var i = 0; i < filelist.length; i++) {
                    list.push(filelist[i]);
                    totalSize += filelist[i].size;
                }
                uploadNext();
            }

            // on complete - start next file
            function handleComplete(size) {
                totalProgress += size;
                drawProgress(totalProgress / totalSize);
                uploadNext();
            }

            // update progress
            function handleProgress(event) {
                var progress = totalProgress + event.loaded;
                drawProgress(progress / totalSize);
            }

            // upload file
            function uploadFile(file, status) {

                // prepare XMLHttpRequest
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'auto_dropzone.php');
                xhr.onload = function() {
                    result.innerHTML += this.responseText;
                    handleComplete(file.size);
                };
                xhr.onerror = function() {
                    result.textContent = this.responseText;
                    handleComplete(file.size);
                };
                xhr.upload.onprogress = function(event) {
                    handleProgress(event);
                }
                xhr.upload.onloadstart = function(event) {
                }

                // prepare FormData
                var formData = new FormData();  
                formData.append('myfile', file); 
                xhr.send(formData);
            }

            // upload next file
            function uploadNext() {
                if (list.length) {
                    dropArea.className = 'DD_dropzone DD_uploading';
                    var nextFile = list.shift();
                    if (nextFile.size >= <?php echo $auto_dropzone['max_length']*1048576; ?>) { 
                        result.innerHTML += '<li class="DD_error">'+nextFile.name+': Error, max filelength: <?php echo $auto_dropzone['max_length'];?> Mo </li>';
                        handleComplete(nextFile.size);
                    } else if(is_allowed(nextFile.type)==false){
                        result.innerHTML += '<li class="DD_error">'+nextFile.name+': Error, forbidden file format: <?php echo $auto_dropzone['allowed_filetypes'];?> only </li>';
                        handleComplete(nextFile.type);
                    } else {
                        uploadFile(nextFile, status);
                    }
                } else {
                    dropArea.className = 'DD_dropzone'
                    bar.style.width='0';
                }
            }

            initHandlers();
        })();

    </script>
<?php }


 ?>
