<?php
namespace verbb\navigation\elements\db;

use craft\elements\db\ElementQuery;

class NodeQuery extends ElementQuery
{
    // Properties
    // =========================================================================

    public mixed $id = null;
    public mixed $elementId = null;
    public mixed $siteId = null;
    public mixed $navId = null;
    public mixed $enabled = true;
    public mixed $type = null;
    public mixed $classes = null;
    public mixed $customAttributes = null;
    public mixed $data = null;
    public mixed $urlSuffix = null;
    public mixed $newWindow = false;
    public mixed $element = null;
    public mixed $handle = null;
    public mixed $hasUrl = false;
}
