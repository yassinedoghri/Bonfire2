<?php

namespace Tests\Bonfire\Menus;

use Bonfire\Libraries\Menus\MenuItem;
use Tests\Support\TestCase;

class MenuItemTest extends TestCase
{
    public function testBasicDetails()
    {
        $item = new MenuItem();
        $item->setTitle('Item A')
            ->setUrl('example.com/foo?bar=baz')
            ->setAltText('Alternate A')
            ->setWeight(5)
            ->setIconUrl('example.com/img/foo.jpg');

        $this->assertEquals('Item A', $item->title());
        $this->assertEquals('/example.com/foo?bar=baz', $item->url());
        $this->assertEquals('Alternate A', $item->altText());
        $this->assertEquals('<img href="/example.com/img/foo.jpg" alt="Item A" />', $item->icon());
        $this->assertEquals(5, $item->weight());
    }

    public function testConstructorFill()
    {
        $item = new MenuItem([
            'title' => 'Item A',
            'url' => 'example.com/foo?bar=baz',
            'altText' => 'Alternate A',
            'weight' => 5,
            'iconUrl' => 'example.com/img/foo.jpg'
]       );

        $this->assertEquals('Item A', $item->title());
        $this->assertEquals('/example.com/foo?bar=baz', $item->url());
        $this->assertEquals('Alternate A', $item->altText());
        $this->assertEquals('<img href="/example.com/img/foo.jpg" alt="Item A" />', $item->icon());
        $this->assertEquals(5, $item->weight());
    }

    public function testBuildsFontAwesomeTag()
    {
        $item = new MenuItem();

        $this->assertEquals('', $item->icon());

        $item->setFontAwesomeIcon('fa-envelope');

        $this->assertEquals('<i class="fa-envelope"></i>', $item->icon());
    }

    public function testBuildsFontAwesomeTagWithExtraClass()
    {
        $item = new MenuItem();

        $this->assertEquals('', $item->icon());

        $item->setFontAwesomeIcon('fa-envelope');

        $this->assertEquals('<i class="fa-envelope extra"></i>', $item->icon('extra'));
    }

    public function testPrefersFontAwesomeOverImg()
    {
        $item = new MenuItem();
        $item->setFontAwesomeIcon('fa-envelope')
            ->setIconUrl('example.com/img/foo.jpg');

        $this->assertEquals('<i class="fa-envelope"></i>', $item->icon());
    }

    public function testImageTagWithExtraClass()
    {
        $item = new MenuItem();
        $item->setTitle('Item A')
             ->setIconUrl('example.com/img/foo.jpg');

        $this->assertEquals('<img href="/example.com/img/foo.jpg" alt="Item A" class="extra" />', $item->icon('extra'));
    }

    public function testWithNamedRoutes()
    {
        $routes = service('routes');
        $routes->get('home-sweet-home', 'HomeController::index', ['as' => 'home']);

        $item = new MenuItem();
        $item->setNamedRoute('home');

        $this->assertEquals('/home-sweet-home', $item->url());
    }

    public function testPropertyGetter()
    {
        $item = new MenuItem(['title' => 'Item 1', 'fontAwesomeIcon' => 'fa-envelope']);

        $this->assertEquals('Item 1', $item->title);
        $this->assertEquals('<i class="fa-envelope"></i>', $item->icon);
    }
}
