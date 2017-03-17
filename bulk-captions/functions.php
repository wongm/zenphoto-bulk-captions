<?php

function isBulkCaptionDateMode()
{
    return !isset($_GET['order']) OR $_GET['order'] == 'date';
}

function isBulkCaptionAlbumMode()
{
    return isset($_GET['order']) && $_GET['order'] == 'album';
}

function getBulkCaptionCurrentPage()
{
    if (isset($_GET['subpage']))
        return $_GET['subpage'];

    return 1;
}

function initBulkCaptionData()
{
	$where = "i.title = SUBSTRING_INDEX(i.filename,'.',1)";
    if (isBulkCaptionDateMode())
    {
        $orderBy = "i.date";
    }
    else
    {
        $orderBy = "a.title";
    }

    // hack to make adminPageNav() work
    $_GET['page'] = $_GET['subpage'];
    
    // hack to make photostream display hardcoded number of images
    setOption('photostream_images_per_page', 10, false);
    
    setCustomPhotostream($where, "", $orderBy);
}

function saveBulkCaptions()
{
    if (!isset($_POST["save"]) && !isset($_POST["imageCount"]))
        return;

    global $completedActionMessages;
    $completedActionMessages = [];
    
    $imageCount = $_POST["imageCount"];
    
    for ($imageID = 1; $imageID <= $imageCount; $imageID++)
    {
        $filename = $_POST["filename_" . $imageID];
        $folder = $_POST["folder_" . $imageID];
        $title = $_POST["title_" . $imageID];
        $originalTitle = $_POST["originalTitle_" . $imageID];
        $description = $_POST["description_" . $imageID];
        $originalDescription = $_POST["originalDescription_" . $imageID];
        
        $titleEdited = $descriptionEdited = false;
        
        if ($title != $originalTitle)
        {
	        $updateSql = "UPDATE " . prefix('images') . " i " . 
	            " INNER JOIN " . prefix('albums') . " a ON i.albumid = a.id " . 
	            " SET i.`title` = " . db_quote($title)  . 
    	        " WHERE i.filename = '" . $filename . "' AND a.folder = '" . $folder . "'";
    	    query_full_array ($updateSql);
    	    $titleEdited = true;
        }
        
        if ($description != $originalDescription)
        {
	        $updateSql = "UPDATE " . prefix('images') . " i " . 
	            " INNER JOIN " . prefix('albums') . " a ON i.albumid = a.id " . 
	            " SET i.`desc` = " . db_quote($description)  . 
    	        " WHERE i.filename = '" . $filename . "' AND a.folder = '" . $folder . "'";
	        query_full_array ($updateSql);
    	    $descriptionEdited = true;
        }
        
        if ($titleEdited && $descriptionEdited) {
            $completedActionMessages[] = "Saved title and description: $filename";
        } else if ($descriptionEdited) {
            $completedActionMessages[] = "Saved description: $filename";
        } else if ($titleEdited) {
            $completedActionMessages[] = "Saved title: $filename";
        }
    }
}

function displayBulkCaptionProcessingResults()
{
    if (getNumPhotostreamImages() == 0) {
        echo "<div class=\"messagebox\">No images to caption!</div>";
    }
    
    if (!isset($_POST["save"])) {
        return;
    }
    
    echo "<div class=\"messagebox fade-message\">";
    
    global $completedActionMessages;
    if (sizeof($completedActionMessages) == 0) {
        echo "No images updated!";
    }
    
    foreach ($completedActionMessages AS $message)
    {
        echo "$message<br>";
    }
    echo "</div>";
}

?>
