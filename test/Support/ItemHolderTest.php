<?php


namespace SzuniSoft\SzamlazzHu\Tests\Support;


use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase;
use SzuniSoft\SzamlazzHu\Tests\Fixtures\Item;
use SzuniSoft\SzamlazzHu\Tests\Fixtures\ItemCollection;
use SzuniSoft\SzamlazzHu\Tests\Fixtures\ItemHolder;

class ItemHolderTest extends TestCase {

    /**
     * @var ItemHolder
     */
    protected $holder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->holder = new ItemHolder();
    }

    /**
     * @return array
     */
    protected function itemArray()
    {
        return [
            'name' => 'Product',
            'quantity' => 5,
            'quantityUnit' => 'piece',
            'netUnitPrice' => 100,
            'taxRate' => 13
        ];
    }

    /**
     * @return Item
     */
    protected function item()
    {
        return new Item(...array_values($this->itemArray()));
    }

    /**
     * @param int $times
     * @return array
     */
    protected function items($times = 1)
    {
        return Collection::times($times, function () {
            return $this->item();
        })->toArray();
    }

    /** @test */
    public function it_can_tell_it_has_no_items()
    {
        $this->assertFalse($this->holder->hasItem());
    }

    /** @test */
    public function it_can_add_contract_item()
    {
        $this->holder->addItem($this->item());
        $this->assertEquals(1, $this->holder->items()->count() );
    }

    /** @test */
    public function it_can_add_array_item()
    {
        $this->holder->addItem($this->itemArray());
        $this->assertEquals(1,$this->holder->items()->count());
        $this->assertSame($this->holder->items()->first(), $this->itemArray());
    }

    /** @test */
    public function it_can_add_multiple_items()
    {
        $this->holder->addItems($this->items(5));
        $this->assertEquals(5, $this->holder->items()->count());
    }

    /** @test */
    public function it_can_add_item_contract_collection()
    {
        $this->holder->addItems(new ItemCollection($this->items(5)));
        $this->assertEquals(5, $this->holder->items()->count());
    }

    /** @test */
    public function it_can_add_numeric_item_collection()
    {
        $this->holder->addItems($this->items(5));
        $this->assertEquals(5, $this->holder->items()->count());
    }

    /** @test */
    public function it_can_remove_item_when_id_is_provided()
    {
        $item = $this->item();
        $item->id = 1;

        $this->holder->addItem($item);
        $this->assertEquals(1, $this->holder->items()->count());
        $this->holder->removeItem(1);
        $this->assertEquals(0, $this->holder->items()->count());
    }

    /** @test */
    public function it_has_total_count_shortcut()
    {
        $this->holder->addItems($this->items(10));
        $this->assertEquals(10, $this->holder->items()->count());
        $this->assertEquals(10, $this->holder->total());
    }

    /** @test */
    public function it_can_provide_numeric_array_representation()
    {

        $items = $this->items();

        $itemsArray = array_map(function (Item $value) {
            return $value->toItemArray();
        }, $items);

        $this->holder->addItems($items);
        $this->assertSame(
            $itemsArray,
            $this->holder->itemsToArray()
        );
    }

    /** @test */
    public function it_can_determine_it_does_not_have_item()
    {
        $this->assertFalse($this->holder->hasItem());
        $this->assertTrue($this->holder->isEmpty());
    }

}
