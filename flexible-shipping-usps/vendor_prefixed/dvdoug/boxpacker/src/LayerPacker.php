<?php

/**
 * Box packing (3D bin packing, knapsack problem).
 *
 * @author Doug Wright
 */
declare (strict_types=1);
namespace FlexibleShippingUspsVendor\DVDoug\BoxPacker;

use FlexibleShippingUspsVendor\Psr\Log\LoggerAwareInterface;
use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\Psr\Log\NullLogger;
use function array_merge;
use function iterator_to_array;
use function max;
use function sort;
/**
 * Layer packer.
 *
 * @internal
 */
class LayerPacker implements \FlexibleShippingUspsVendor\Psr\Log\LoggerAwareInterface
{
    private \FlexibleShippingUspsVendor\Psr\Log\LoggerInterface $logger;
    private \FlexibleShippingUspsVendor\DVDoug\BoxPacker\Box $box;
    private bool $singlePassMode = \false;
    private \FlexibleShippingUspsVendor\DVDoug\BoxPacker\OrientatedItemFactory $orientatedItemFactory;
    private bool $beStrictAboutItemOrdering = \false;
    private bool $isBoxRotated = \false;
    public function __construct(\FlexibleShippingUspsVendor\DVDoug\BoxPacker\Box $box)
    {
        $this->box = $box;
        $this->logger = new \FlexibleShippingUspsVendor\Psr\Log\NullLogger();
        $this->orientatedItemFactory = new \FlexibleShippingUspsVendor\DVDoug\BoxPacker\OrientatedItemFactory($this->box);
        $this->orientatedItemFactory->setLogger($this->logger);
    }
    /**
     * Sets a logger.
     */
    public function setLogger(\FlexibleShippingUspsVendor\Psr\Log\LoggerInterface $logger) : void
    {
        $this->logger = $logger;
        $this->orientatedItemFactory->setLogger($logger);
    }
    public function setSinglePassMode(bool $singlePassMode) : void
    {
        $this->singlePassMode = $singlePassMode;
        $this->orientatedItemFactory->setSinglePassMode($singlePassMode);
    }
    public function setBoxIsRotated(bool $boxIsRotated) : void
    {
        $this->isBoxRotated = $boxIsRotated;
        $this->orientatedItemFactory->setBoxIsRotated($boxIsRotated);
    }
    public function beStrictAboutItemOrdering(bool $beStrict) : void
    {
        $this->beStrictAboutItemOrdering = $beStrict;
    }
    /**
     * Pack items into an individual vertical layer.
     */
    public function packLayer(\FlexibleShippingUspsVendor\DVDoug\BoxPacker\ItemList &$items, \FlexibleShippingUspsVendor\DVDoug\BoxPacker\PackedItemList $packedItemList, int $startX, int $startY, int $startZ, int $widthForLayer, int $lengthForLayer, int $depthForLayer, int $guidelineLayerDepth, bool $considerStability) : \FlexibleShippingUspsVendor\DVDoug\BoxPacker\PackedLayer
    {
        $layer = new \FlexibleShippingUspsVendor\DVDoug\BoxPacker\PackedLayer();
        $x = $startX;
        $y = $startY;
        $z = $startZ;
        $rowLength = 0;
        $prevItem = null;
        $skippedItems = [];
        $remainingWeightAllowed = $this->box->getMaxWeight() - $this->box->getEmptyWeight() - $packedItemList->getWeight();
        while ($items->count() > 0) {
            $itemToPack = $items->extract();
            // skip items that will never fit e.g. too heavy
            if (!$this->checkNonDimensionalConstraints($itemToPack, $remainingWeightAllowed, $packedItemList)) {
                continue;
            }
            $orientatedItem = $this->orientatedItemFactory->getBestOrientation($itemToPack, $prevItem, $items, $widthForLayer - $x, $lengthForLayer - $y, $depthForLayer, $rowLength, $x, $y, $z, $packedItemList, $considerStability);
            if ($orientatedItem instanceof \FlexibleShippingUspsVendor\DVDoug\BoxPacker\OrientatedItem) {
                $packedItem = \FlexibleShippingUspsVendor\DVDoug\BoxPacker\PackedItem::fromOrientatedItem($orientatedItem, $x, $y, $z);
                $layer->insert($packedItem);
                $packedItemList->insert($packedItem);
                $rowLength = \max($rowLength, $packedItem->getLength());
                $prevItem = $orientatedItem;
                // Figure out if we can stack items on top of this rather than side by side
                // e.g. when we've packed a tall item, and have just put a shorter one next to it.
                $stackableDepth = ($guidelineLayerDepth ?: $layer->getDepth()) - $packedItem->getDepth();
                if ($stackableDepth > 0) {
                    $stackedLayer = $this->packLayer($items, $packedItemList, $x, $y, $z + $packedItem->getDepth(), $x + $packedItem->getWidth(), $y + $packedItem->getLength(), $stackableDepth, $stackableDepth, $considerStability);
                    $layer->merge($stackedLayer);
                }
                $x += $packedItem->getWidth();
                $remainingWeightAllowed = $this->box->getMaxWeight() - $this->box->getEmptyWeight() - $packedItemList->getWeight();
                // remember may have packed additional items
                // might be space available lengthwise across the width of this item, up to the current layer length
                $layer->merge($this->packLayer($items, $packedItemList, $x - $packedItem->getWidth(), $y + $packedItem->getLength(), $z, $x, $y + $rowLength, $depthForLayer, $layer->getDepth(), $considerStability));
                if ($items->count() === 0 && $skippedItems) {
                    $items = \FlexibleShippingUspsVendor\DVDoug\BoxPacker\ItemList::fromArray(\array_merge($skippedItems, \iterator_to_array($items)), \true);
                    $skippedItems = [];
                }
                continue;
            }
            if (!$this->beStrictAboutItemOrdering && $items->count() > 0) {
                // skip for now, move on to the next item
                $this->logger->debug("doesn't fit, skipping for now");
                $skippedItems[] = $itemToPack;
                // abandon here if next item is the same, no point trying to keep going. Last time is not skipped, need that to trigger appropriate reset logic
                while ($items->count() > 1 && self::isSameDimensions($itemToPack, $items->top())) {
                    $skippedItems[] = $items->extract();
                }
                continue;
            }
            if ($x > $startX) {
                $this->logger->debug('No more fit in width wise, resetting for new row');
                $y += $rowLength;
                $x = $startX;
                $rowLength = 0;
                $skippedItems[] = $itemToPack;
                $items = \FlexibleShippingUspsVendor\DVDoug\BoxPacker\ItemList::fromArray(\array_merge($skippedItems, \iterator_to_array($items)), \true);
                $skippedItems = [];
                $prevItem = null;
                continue;
            }
            $this->logger->debug('no items fit, so starting next vertical layer');
            $skippedItems[] = $itemToPack;
            $items = \FlexibleShippingUspsVendor\DVDoug\BoxPacker\ItemList::fromArray(\array_merge($skippedItems, \iterator_to_array($items)), \true);
            return $layer;
        }
        return $layer;
    }
    /**
     * As well as purely dimensional constraints, there are other constraints that need to be met
     * e.g. weight limits or item-specific restrictions (e.g. max <x> batteries per box).
     */
    private function checkNonDimensionalConstraints(\FlexibleShippingUspsVendor\DVDoug\BoxPacker\Item $itemToPack, int $remainingWeightAllowed, \FlexibleShippingUspsVendor\DVDoug\BoxPacker\PackedItemList $packedItemList) : bool
    {
        $customConstraintsOK = \true;
        if ($itemToPack instanceof \FlexibleShippingUspsVendor\DVDoug\BoxPacker\ConstrainedItem && !$this->box instanceof \FlexibleShippingUspsVendor\DVDoug\BoxPacker\WorkingVolume) {
            $customConstraintsOK = $itemToPack->canBePackedInBox($packedItemList, $this->box);
        }
        return $customConstraintsOK && $itemToPack->getWeight() <= $remainingWeightAllowed;
    }
    /**
     * Compare two items to see if they have same dimensions.
     */
    private static function isSameDimensions(\FlexibleShippingUspsVendor\DVDoug\BoxPacker\Item $itemA, \FlexibleShippingUspsVendor\DVDoug\BoxPacker\Item $itemB) : bool
    {
        if ($itemA === $itemB) {
            return \true;
        }
        $itemADimensions = [$itemA->getWidth(), $itemA->getLength(), $itemA->getDepth()];
        $itemBDimensions = [$itemB->getWidth(), $itemB->getLength(), $itemB->getDepth()];
        \sort($itemADimensions);
        \sort($itemBDimensions);
        return $itemADimensions === $itemBDimensions;
    }
}