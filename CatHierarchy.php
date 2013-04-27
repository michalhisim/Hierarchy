<?php

namespace Tree;

/**
 * Nette Hierarchy
 *
 * @copyright Copyright (c) 2012 Michal Šimon
 */
use \Nette\Diagnostics\Debugger;

class CatHierarchy extends \Tree\Hierarchy {

    function __construct(\Nette\Database\Statement $data) {
        $this->data = $data;
        $this->nodeClass = '\Tree\CatNode';
    }

    /**
     * Build a tree from data "table".
     * @return tree array 
     */
    protected function makeTree($data = NULL) {
        $list = NULL;

        if ($data != NULL) {
            $list = $data;
        } else {
            $list = $this->data;
        }

        $count = 0;

        foreach ($list AS $row) {
            $node = new $this->nodeClass($row);

            if ($node->rootId == 0) {
                // Level 0
                $this->tree[$node->alias] = $node;
            } else {
                // SubLevels
                $added = false;

                foreach ($this->tree AS $nodeRow) {
                    if ($nodeRow->addChild($node)) {
                        $added = true;
                        break;
                    }
                }

                if ($added == false) {
                    $this->notSet[] = $row;
                }
            }

            $count++;
        }

        if ($count > $this->maxLevel) {
            // first tree making
            $this->maxLevel = $count;
            $this->treeIterator++;
        }

        if (!empty($this->notSet) AND ($this->treeIterator <= $this->maxLevel)) {
            $this->makeTree($this->notSet); // todo Kontrola logiky, zda se nemůže rekurze zacyklit

            $this->treeIterator++;
        }

        return $this->tree;
    }

    public function findLink($catId, $cats = false) {
        $url = array();

        if (!$cats) {
            $cats = $this->getTree();
        }

        foreach ($cats AS $cat) {
            if ($cat->type == \ProductModel::CAT_TYPE_SUPER) {
                if (!empty($cat->children)) {

                    if ($result = $this->findLink($catId, $cat->children)) {

                        $url = array_merge($url, $result);
                    }
                }
                continue;
            }

            if ($cat->id == $catId) {
                $url = array_merge($url, array($cat->alias));
                break;
            } else {
                if (!empty($cat->children)) {
                    if ($result = $this->findLink($catId, $cat->children)) {
                        $url[] = $cat->alias;

                        $url = array_merge($url, $result);
                    }
                }
            }
        }

        return $url;
    }

}
