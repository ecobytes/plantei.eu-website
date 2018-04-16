<?php

namespace Caravel\Support;
//https://stackoverflow.com/a/34038151

trait DynamicHiddenVisible {

    public static $_hidden = null;
    public static $_visible = null;

    public static function setStaticHidden(array $value) {
        self::$_hidden = $value;
        return self::$_hidden;
    }

    public static function getStaticHidden() {
        return self::$_hidden;
    }

    public static function setStaticVisible(array $value) {
        self::$_visible = $value;
        return self::$_visible;
    }

    public static function getStaticVisible() {
        return self::$_visible;
    }

    public static function getDefaultHidden() {
        return with(new static)->getHidden();
    }

    public static function getDefaultVisible() {
        return with(new static)->getVisible();
    }

    public function toArray()    {
        if (self::getStaticVisible())
            $this->visible = self::getStaticVisible();
        else if (self::getStaticHidden())
            $this->hidden = self::getStaticHidden();
        return parent::toArray();
    }

}
