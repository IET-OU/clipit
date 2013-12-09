<?php
/**
 * URJC Backend
 * PHP version:     >= 5.2
 * Creation date:   2013-11-01
 * Last update:     $Date$
 *
 * @author          Pablo Llinás Arnaiz <pebs74@gmail.com>, JuxtaLearn Project
 * @version         $Version$
 * @link            http://
 * @license         GNU Affero General Public License v3
 *                  (http://www.gnu.org/licenses/agpl-3.0.txt)
 *                  This program is free software: you can redistribute it and/or modify
 *                  it under the terms of the GNU Affero General Public License as
 *                  published by the Free Software Foundation, version 3.
 *                  This program is distributed in the hope that it will be useful,
 *                  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *                  GNU Affero General Public License for more details.
 *                  You should have received a copy of the GNU Affero General Public License
 *                  along with this program. If not, see
 *                  http://www.gnu.org/licenses/agpl-3.0.txt.
 */

/**
 * Class UBUser
 *
 * @package urjc_backend
 */
class UBFile extends UBItem{
    /**
     * @const string Elgg entity SUBTYPE for this class
     */
    const SUBTYPE = "file";
    /**
     * @var string File data in byte string format
     */
    public $data = null;
    /**
     * @var int Timestamp when the user uploaded this File
     */
    public $time_created = -1;

    /**
     * Loads an instance from the system.
     *
     * @param int $id Id of the instance to load from the system.
     * @return UBFile|bool Returns instance, or false if error.
     */
    protected function _load($id){
        if(!($elgg_file = new ElggFile((int)$id))){
            return null;
        }
        $this->id = (int)$elgg_file->guid;
        $this->name = (string)$elgg_file->getFilename();
        $this->description = (string)$elgg_file->description;
        $this->data = $elgg_file->grabFile();
        $this->time_created = (int)$elgg_file->time_created;
        return $this;
    }

    /**
     * Saves this instance to the system.
     *
     * @return bool|int Returns id of saved instance, or false if error.
     */
    function save(){
        if($this->id == -1){
            $elgg_file = new ElggFile();
        } elseif(!$elgg_file = new ElggFile((int)$this->id)){
            return false;
        }
        $elgg_file->setFilename((string)$this->name);
        $elgg_file->description = (string)$this->description;
        $elgg_file->open("write");
        $elgg_file->write($this->data);
        $elgg_file->close();
        $elgg_file->save();
        return $this->id = $elgg_file->guid;
    }
}