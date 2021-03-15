<?php

namespace BigFish\PDF417\Renderers;

use BigFish\PDF417\BarcodeData;
use BigFish\PDF417\RendererInterface;

abstract class AbstractRenderer implements RendererInterface
{
    /** Default options array. */
    protected $options = [];

    public function __construct(array $options = [])
    {
        // Merge options with defaults, ignore options not specified in
        // defaults.
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            }
        }

        $errors = $this->validateOptions();
        if (!empty($errors)) {
            $errors = implode("\n", $errors);
            throw new \InvalidArgumentException($errors);
        }
    }

    /**
     * Validates the options, throws an Exception on failure.
     *
     * @param  array $options
     * @return array An array of errors, empty if no errors.
     */
    public function validateOptions()
    {
        return [];
    }

    /**
     * Returns the MIME content type of the barcode generated by this renderer.
     *
     * @return string
     */
    public abstract function getContentType();

    /**
     * Renders the barcode from the given data set.
     *
     * @param  BarcodeData $data  The barcode data.
     * @return mixed              Output format depends on the renderer.
     */
    public abstract function render(BarcodeData $data);
}
