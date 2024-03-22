<?php

function isBulkCaptionDateMode()
{
    return (getBulkCaptionOrder() == 'date');
}

function isBulkCaptionAlbumMode()
{
    return !isBulkCaptionDateMode();
}

function getBulkCaptionOrder()
{
    if (isset($_GET['order']) && $_GET['order'] == 'album')
    {
        return 'album';
    }
    else
    {
        return 'date';
    }
}

function getBulkCaptionCurrentPage()
{
    if (isset($_GET['subpage'])) {
        return $_GET['subpage'];
    }

    return 1;
}

function initBulkCaptionData($firstLoad=true)
{
	$where = "i.title = SUBSTRING_INDEX(i.filename,'.',1)";
    if (isBulkCaptionDateMode()) {
        $orderBy = "i.date";
    } else {
        $orderBy = "a.title, i.date";
    }

    // hack to make adminPageNav() work
    if (isset($_GET["subpage"])) {
        $_GET['page'] = $_GET['subpage'];
    }
    
    // hack to make photostream display hardcoded number of images
    setOption('photostream_images_per_page', 10, false);
    
    setCustomPhotostream($where, "", $orderBy);
    
    // search for images if nothing found and reload if required
    if (getNumPhotostreamImages() == 0 && $firstLoad == true) {
        global $_zp_gallery;
        $_zp_gallery->getAlbums();
        initBulkCaptionData(false);
    } else if (getNumPhotostreamImages() > 0 && !isset($_GET["subpage"]) && (!isset($_GET["mode"]) || $_GET["mode"] != 'summary') && file_exists(dirname(__FILE__) . '/../daily-summary/index.php')) {
        header('Location: /plugins/daily-summary/?mode=bulk');
        die();
    }
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
        $completedActionMessage = saveBulkCaptionForImage($imageID);
        
        if (strlen($completedActionMessage) > 0) {
            $completedActionMessages[] = $completedActionMessage;
        }
    }
    
    $completedActionMessage = postSaveAction();
    if (strlen($completedActionMessage) > 0) {
        $completedActionMessages[] = $completedActionMessage;
    }
}

function postSaveAction()
{
	global $_zp_db;
	
    $hiddenCaptionedImageQuery = "SELECT count(1) AS `hiddenUncaptionedImageCount` 
        FROM " . $_zp_db->prefix('images') . " i 
        WHERE i.`show` = 0 AND i.title = SUBSTRING_INDEX(i.filename,'.',1)";
        
    $hiddenUncaptionedImageQuery = "SELECT count(1) AS `hiddenCaptionedImageCount` 
        FROM " . $_zp_db->prefix('images') . " i 
        WHERE i.`show` = 0 AND i.title != SUBSTRING_INDEX(i.filename,'.',1)";
    
    $hiddenUncaptionedImageCount = $_zp_db->querySingleRow($hiddenCaptionedImageQuery)['hiddenUncaptionedImageCount'];
    
	$hiddenCaptionedImageCount = $_zp_db->querySingleRow($hiddenUncaptionedImageQuery)['hiddenCaptionedImageCount'];
	
    if ($hiddenUncaptionedImageCount == 0 && $hiddenCaptionedImageCount > 0)
    {        
        $updateUnpublishedCaptionedImagesQuery = "UPDATE " . $_zp_db->prefix('images') . " i 
            SET i.`show` = 1 
            WHERE i.`show` = 0 
            AND i.title != SUBSTRING_INDEX(i.filename,'.',1)";

        $_zp_db->query($updateUnpublishedCaptionedImagesQuery);

	    require_once(SERVERPATH . '/' . ZENFOLDER . '/' . PLUGIN_FOLDER . '/static_html_cache.php');
	    static_html_cache::clearHTMLCache();
	    
	    return "$hiddenCaptionedImageCount new images published, cache cleared";
    }
}

function publishPhotos()
{
    publishPhotos();
    clearCache();
}

function clearCache()
{
    require_once(SERVERPATH . '/' . ZENFOLDER . '/' . PLUGIN_FOLDER . '/static_html_cache.php');
    static_html_cache::clearHTMLCache();
}

function saveBulkCaptionForImage($imageID)
{
	global $_zp_db;
	
    if (!isset($_POST["filename_" . $imageID])) {
        return;
    }

    $filename = $_POST["filename_" . $imageID];
    $folder = $_POST["folder_" . $imageID];
    $title = $_POST["title_" . $imageID];
    $originalTitle = $_POST["originalTitle_" . $imageID];
    $description = $_POST["description_" . $imageID];
    $originalDescription = $_POST["originalDescription_" . $imageID];
    
    $titleEdited = $descriptionEdited = $dailyScoreEdited = false;
    
    if ($title != $originalTitle)
    {
        $updateSql = "UPDATE " . $_zp_db->prefix('images') . " i " . 
            " INNER JOIN " . $_zp_db->prefix('albums') . " a ON i.albumid = a.id " . 
            " SET i.`title` = " . $_zp_db->quote($title)  . 
	        " WHERE i.filename = '" . $filename . "' AND a.folder = '" . $folder . "'";
	    $_zp_db->queryFullArray($updateSql);
	    $titleEdited = true;
    }
    
    if ($description != $originalDescription)
    {
        $updateSql = "UPDATE " . $_zp_db->prefix('images') . " i " . 
            " INNER JOIN " . $_zp_db->prefix('albums') . " a ON i.albumid = a.id " . 
            " SET i.`desc` = " . $_zp_db->quote($description)  . 
	        " WHERE i.filename = '" . $filename . "' AND a.folder = '" . $folder . "'";
        $_zp_db->queryFullArray($updateSql);
	    $descriptionEdited = true;
    }
	
	if (isset($_POST["daily_score_" . $imageID])) {
		$dailyScore = $_POST["daily_score_" . $imageID];
		$updateSql = "UPDATE " . $_zp_db->prefix('images') . " i " . 
            " INNER JOIN " . $_zp_db->prefix('albums') . " a ON i.albumid = a.id " . 
            " SET i.daily_score = 1 " .
	        " WHERE i.filename = '" . $filename . "' AND a.folder = '" . $folder . "'";
	    $_zp_db->queryFullArray($updateSql);
	    $dailyScoreEdited = true;
	}
	
	$dailyScoreEditedMessage = "";
	if ($dailyScoreEdited) {
		$dailyScoreEditedMessage = ". Image flagged for daily summary.";
	}
    
    $completedActionMessages = "";
    if ($titleEdited && $descriptionEdited) {
        $completedActionMessages = "Saved title and description: $filename$dailyScoreEditedMessage";
    } else if ($descriptionEdited) {
        $completedActionMessages = "Saved description: $filename$dailyScoreEditedMessage";
    } else if ($titleEdited) {
        $completedActionMessages = "Saved title: $filename$dailyScoreEditedMessage";
    }
    
    return $completedActionMessages;
}

function displayBulkCaptionProcessingResults()
{
    if (getNumPhotostreamImages() == 0) {
        echo "<div class=\"messagebox\">No images to caption!</div>";
    }
    
    global $completedActionMessages;
    
    if (!isset($_POST["save"])) {
        return;
    }
    
    echo "<div class=\"messagebox fade-message\">";
    
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