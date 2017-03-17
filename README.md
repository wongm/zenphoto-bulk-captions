# zenphoto-bulk-captions
Plugin for the Zenphoto open-source gallery that enables the bulk captioning of newly uploaded images.

# Installation
1. Install my 'Zenphoto Photostream' plugin (https://github.com/wongm/zenphoto-photostream)
2. Copy bulk-captions.php and the /bulk-captions folder into the /plugins directory of your Zenphoto installation.
3. Enable the 'bulk captions' plugin in the Zenphoto backend
4. A new 'Bulk captions' link will appear on the Zenphoto admin home page

# Usage

Visiting the 'Bulk captions' link will display a list of all images that currently lack captions.
Images can be ordered by date or by album.
Enter title and description for each images, then click save.
Details will then be saved, and the captioned images will disappear from the list.

NOTE: this plugin works on the assumption that an 'uncaptioned' image is one where the title is the same as the filename, and the filename only includes a single '.' character.