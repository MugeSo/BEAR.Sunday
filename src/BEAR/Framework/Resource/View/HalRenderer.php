<?php
/**
 * BEAR.Framework
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace BEAR\Framework\Resource\View;

use BEAR\Framework\Resource\Link;
use BEAR\Resource\Object as ResourceObject;
use BEAR\Resource\Requestable;
use BEAR\Resource\Renderable;
use Nocarrier\Hal;

/**
 * Request renderer
 *
 * @package    BEAR.Framework
 * @subpackage View
 */
class HalRenderer implements Renderable
{
    /**
     * (non-PHPdoc)
     * @see BEAR\Resource.Renderable::render()
     */
    public function render(ResourceObject $ro)
    {
        // evaluate all request in body.
        if (is_array($ro->body) || $ro->body instanceof \Traversable) {
            array_walk_recursive($ro->body, function(&$element) {
                if ($element instanceof Requestable) {
                    $element = $element();
                }
            });
        }

        // HAL
        $data = $ro->body ?: [];
        $hal = new Hal($ro->uri, $data);
        foreach ($ro->links as $rel => $link) {
            $title = (isset($link[Link::TITLE])) ? $link[Link::TITLE] : null;
            $attr = (isset($link[Link::TEMPLATED]) && $link[Link::TEMPLATED] === true) ? [Link::TEMPLATED => true] : [];
            if (isset($link[Link::HREF])) {
                $hal->addLink($rel, $link[Link::HREF], $title, $attr);
            } else {
                trigger_error('"href" is required in link', E_USER_WARNING);
            }
        }
        $ro->view = $hal->asJson(true);
        $ro->headers['content-type'] = 'application/hal+json; charset=UTF-8';

        return $ro->view;
    }
}
