<?php
/**
 * ClipIt - JuxtaLearn Web Space
 * PHP version:     >= 5.2
 * Creation date:   2013-10-10
 * Last update:     $Date$
 * @author          Pablo Llinás Arnaiz <pebs74@gmail.com>, URJC JuxtaLearn Team
 * @version         $Version$
 * @link            http://www.juxtalearn.eu
 * @license         GNU Affero General Public License v3
 * @package         ClipIt
 * @subpackage      urjc_backend
 */

/**
 * Class UBFile
 */
class UBFile extends UBItem{
    /**
     * @const string Elgg entity SUBTYPE for this class
     */
    const SUBTYPE = "file";
    /**
     * @const string Delimiter for timestamp string
     */
    const TIMESTAMP_DELIMITER = "#";
    const DEFAULT_FILENAME = "unnamed_file";
    const THUMB_SMALL = 64;
    const THUMB_NORMAL = 128;
    const THUMB_LARGE = 256;
    /**
     * @var string File data in byte string format.
     */
    public $data = null;
    public $size = 0;
    public $file_path = "";
    public $temp_path = "";
    public $thumb_small = array();
    public $thumb_normal = array();
    public $thumb_large = array();
    public $mime_type = array();


    /* Instance Functions */
    /**
     * Constructor
     *
     * @param int $id If $id is null, create new instance; else load instance with id = $id.
     *
     * @throws APIException
     */
    function __construct($id = null){
        if(!empty($id)){
            if(!($elgg_file = new ElggFile((int)$id))){
                throw new APIException("ERROR: Id '" . $id . "' does not correspond to a " . get_class_name() . " object.");
            }
            $this->load_from_elgg($elgg_file);
        }
    }

    /**
     * @param ElggFile $elgg_file
     */
    protected function load_from_elgg($elgg_file){
        $this->id = (int)$elgg_file->get("guid");
        $this->description = (string)$elgg_file->get("description");
        $this->owner_id = (int)$elgg_file->getOwnerGUID();
        $this->time_created = (int)$elgg_file->getTimeCreated();
        $this->name = (string)$elgg_file->name;
        $this->size = (int)$elgg_file->size();
        $this->file_path = (string)$elgg_file->getFilenameOnFilestore();
        $this->url = (string)elgg_get_site_url()."file/download/".$this->id;
        if(!empty($elgg_file->thumb_small)){
            $this->thumb_small["path"] = (string)$elgg_file->thumb_small;
            $this->thumb_small["url"] = (string)elgg_get_site_url()."file/thumbnail/small/".$this->id;
            $this->thumb_normal["path"] = (string)$elgg_file->thumb_normal;
            $this->thumb_normal["url"] = (string)elgg_get_site_url()."file/thumbnail/normal/".$this->id;
            $this->thumb_large["path"] = (string)$elgg_file->thumb_large;
            $this->thumb_large["url"] = (string)elgg_get_site_url()."file/thumbnail/large/".$this->id;
        }
        if(!empty($elgg_file->mime_type)){
            $this->mime_type["full"] = $elgg_file->mime_type[0];
            $this->mime_type["short"] = $elgg_file->mime_type[1];
        }
    }

    /**
     * Saves this instance to the system.
     *
     * @return bool|int Returns id of saved instance, or false if error.
     */
    protected function save(){
        if(!empty($this->id)){
            if(!$elgg_file = new ElggFile($this->id)){
                return false;
            }
        } else{
            $elgg_file = new ElggFile();
            $elgg_file->type = static::TYPE;
            $elgg_file->subtype = static::SUBTYPE;
        }
        $this->copy_to_elgg($elgg_file);
        $elgg_file->save();
        return $this->id = $elgg_file->guid;
    }



    /**
     * Saves this instance to the system
     * @param ElggFile $elgg_file Elgg file instance to save Item to
     */
    protected function copy_to_elgg($elgg_file){
        if($this->time_created == 0){ // new file
            $elgg_file->filename = (string)rand();
        }
        $elgg_file->name = (string)$this->name;
        $elgg_file->description = (string)$this->description;
        $elgg_file->access_id = ACCESS_PUBLIC;
        if(!empty($this->data)){ // new file or new data
            $elgg_file->open("write");
            if($decoded_data = base64_decode($this->data, true)){
                $elgg_file->write($decoded_data);
            } else{
                $elgg_file->write($this->data);
            }
            $elgg_file->close();
            static::create_thumbnails($elgg_file);
        }
        elseif(!empty($this->temp_path)){ // File was uploaded into local temp dir
            $elgg_file->open("write"); // to ensure file is created in disk
            $elgg_file->close();
            move_uploaded_file($this->temp_path, $elgg_file->getFilenameOnFilestore());
            static::create_thumbnails($elgg_file);
        } else{
            if(!empty($this->thumb_small)){
                $elgg_file->thumb_small = (string)$this->thumb_small["path"];
                $elgg_file->thumb_normal = (string)$this->thumb_normal["path"];
                $elgg_file->thumb_large = (string)$this->thumb_large["path"];
            }
        }
        $filestore_name = $elgg_file->getFilenameOnFilestore();
        $mime_type["full"] = (string)static::get_mime_type($filestore_name);
        if($mime_type["full"] == "application/zip"){ // Detect Office 2007+ mimetype
            $new_mime = getMicrosoftOfficeMimeInfo($filestore_name);
            if($new_mime !== false){
                $mime_type["full"] = (string)$new_mime["mime"];
            }
        }
        $mime_type["short"] = (string)static::get_simple_mime_type($mime_type["full"]);
        $elgg_file->mime_type = (array)$mime_type;
    }

    /**
     * @param string $file Filename on Filestore
     * @return bool|mixed|null|string Mime Type
     */
    static function get_mime_type($file) {
        $mime = false;

        // for PHP5 folks.
        if (function_exists('finfo_file') && defined('FILEINFO_MIME_TYPE')) {
            $resource = finfo_open(FILEINFO_MIME_TYPE);
            if ($resource) {
                $mime = finfo_file($resource, $file);
            }
        }
        // default
        if (!$mime) {
            return null;
        }

        return $mime;
    }




    /**
     * Returns an overall file type from the mimetype
     *
     * @param string $mime_type The MIME type
     * @return string The overall type
     */
    static function get_simple_mime_type($mime_type) {
        switch ($mime_type) {
            case "application/msword":
            case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                return "document";
                break;
            case "application/pdf":
                return "document";
                break;
            case "application/ogg":
                return "audio";
                break;
            case "application/x-rar":
            case "application/zip":
                return "compressed";
                break;
        }

        if (substr_count($mime_type, 'text/')) {
            return "document";
        }

        if (substr_count($mime_type, 'audio/')) {
            return "audio";
        }

        if (substr_count($mime_type, 'image/')) {
            return "image";
        }

        if (substr_count($mime_type, 'video/')) {
            return "video";
        }

        if (substr_count($mime_type, 'opendocument')) {
            return "document";
        }

        return "general";
    }

    /**
     * @param ElggFile $elgg_file
     */
    private static function create_thumbnails($elgg_file){
        $file_name = $elgg_file->getFilename();
        $filestore_name = $elgg_file->getFilenameOnFilestore();
        $simple_mime_type = static::get_simple_mime_type(static::get_mime_type($filestore_name));
        // if image, we need to create thumbnails (this should be moved into a function)
        if ($simple_mime_type == "image") {
            $thumb = new ElggFile();
            $thumbnail = get_resized_image_from_existing_file($filestore_name, static::THUMB_SMALL, static::THUMB_SMALL, false);
            if ($thumbnail) {
                $thumb->setFilename("thumb_small-".$file_name);
                $thumb->open("write");
                $thumb->write($thumbnail);
                $thumb->close();
                $elgg_file->thumb_small = (string)$thumb->getFilenameOnFilestore();
                unset($thumbnail);
            }

            $thumbnail = get_resized_image_from_existing_file($filestore_name, static::THUMB_NORMAL, static::THUMB_NORMAL, false);
            if ($thumbnail) {
                $thumb->setFilename("thumb_normal-".$file_name);
                $thumb->open("write");
                $thumb->write($thumbnail);
                $thumb->close();
                $elgg_file->thumb_normal = (string)$thumb->getFilenameOnFilestore();
                unset($thumbnail);
            }

            $thumbnail = get_resized_image_from_existing_file($filestore_name, static::THUMB_LARGE, static::THUMB_LARGE, false);
            if ($thumbnail) {
                $thumb->setFilename("thumb_large-".$file_name);
                $thumb->open("write");
                $thumb->write($thumbnail);
                $thumb->close();
                $elgg_file->thumb_large = (string)$thumb->getFilenameOnFilestore();
                unset($thumbnail);
            }
        }
    }
}