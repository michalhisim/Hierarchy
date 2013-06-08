<?php

namespace Tree;

/**
 * Nette Hierarchy
 *
 * @copyright Copyright (c) 2012 Michal Šimon
 */
use \Nette\Diagnostics\Debugger;

class HierarchyNode extends \Nette\Object implements IHierarchyNode {

    protected $id = NULL;
    protected $name = NULL;
    protected $level = NULL;
    protected $rootId = NULL;
    protected $children = NULL;

    function __construct(\Nette\Database\Row $data) {
        $this->id = $data->id;
        $this->name = $data->name;
        $this->level = 0;
        $this->rootId = (int) $data->root_id;
    }
    
    public function getId(){
        return $this->id;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getLevel(){
        return $this->level;
    }
    
    public function setLevel($level){
        $this->level = intval($level);
    }
    
    public function getRootId(){
        return $this->rootId;
    }
    
    public function getChildren(){
        return $this->children;
    }

    /**
     * Adding a subnode
     * @param HierarchyNode $child
     * @return bool
     */
    public function addChild(IHierarchyNode $child) {

        if ($child->rootId == $this->id) {
            $child->level = $this->level + 1;

            $this->children[$child->id] = $child;

            return true;
        } else {
            //Not my child -> delegate

            if ($this->children != NULL) {
                foreach ($this->children AS $nodeRow) {

                    if ($nodeRow->addChild($child)) {
                        return true;
                    }
                }
            }

            return false;
        }
    }

    /**
     * Searching in tree
     * @param int $id
     * @return HierarchyNode or false
     */
    public function findChild($id) {
        if ($id == $this->id) {
            return $this;
        } elseif ($this->children != NULL) {
            if (isset($this->children[$id])) {
                return $this->children[$id];
            } else {
                foreach ($this->children AS $child) {
                    if ($result = $child->findChild($id)) {
                        return $result;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Generate array of node IDs as path to node
     * @param int $id
     * @return array of Nodes
     */
    public function getPathTo($id) {
        $path = false;

        if ($id == $this->id) {
            $path[] = $this;
        } elseif ($this->children != NULL) {
            if (isset($this->children[$id])) {
                $path[] = $this;
                $path[] = $this->children[$id];
            } else {
                foreach ($this->children AS $child) {
                    if ($result = $child->getPathTo($id)) {
                        return $result;
                    }
                }
            }
        }

        return $path;
    }

    /**
     * Generate array of all sub node IDs
     * @param int $id
     * @return array of ids
     */
    public function getSubIds() {

        $ids = array($this->id);

        if ($this->children) {
            $result = array();
            
            foreach ($this->children AS $node) {
                $result = array_merge($result, $node->getSubIds());
            }
        }
        
        if (isset($result)) {
            $ids = array_merge($ids, $result);
        }

        return array_unique($ids);
    }

}
