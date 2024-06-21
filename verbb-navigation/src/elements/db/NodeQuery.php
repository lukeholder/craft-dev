<?php
namespace verbb\navigation\elements\db;

use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use verbb\navigation\models\Nav as NavModel;

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


    // Public Methods
    // =========================================================================

    public function init(): void
    {
        if (!isset($this->withStructure)) {
            $this->withStructure = true;
        }

        parent::init();
    }

    public function elementId($value): static
    {
        $this->elementId = $value;
        return $this;
    }

    public function elementSiteId($value): static
    {
        $this->slug = $value;
        return $this;
    }

    public function navId($value): static
    {
        $this->navId = $value;
        return $this;
    }

    public function navHandle($value): static
    {
        $this->handle = $value;
        return $this;
    }

    public function type($value): static
    {
        $this->type = $value;
        return $this;
    }

    public function element($value): static
    {
        $this->element = $value;
        return $this;
    }

    public function handle($value): static
    {
        $this->handle = $value;
        return $this;
    }

    public function hasUrl(bool $value = false): static
    {
        $this->hasUrl = $value;
        return $this;
    }


    // Protected Methods
    // =========================================================================
}
