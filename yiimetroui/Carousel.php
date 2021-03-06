<?php
/**
 * This class is merely used to publish assets that are needed by all dhtmlx
 * widgets and thus have to be imported before any widget gets rendered.
 * @copyright Frenzel GmbH - www.frenzel.net
 * @link http://www.frenzel.net
 * @author Philipp Frenzel <philipp@frenzel.net>
 */

namespace yiimetroui;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Carousel renders a carousel bootstrap javascript component.
 *
 * For example:
 *
 * ```php
 * echo Carousel::widget(array(
 *     'items' => array(
 *         // the item contains only the image
 *         '<img src="http://twitter.github.io/bootstrap/assets/img/bootstrap-mdo-sfmoma-01.jpg"/>',
 *         // equivalent to the above
 *         array(
 *             'content' => '<img src="http://twitter.github.io/bootstrap/assets/img/bootstrap-mdo-sfmoma-02.jpg"/>',
 *         ),
 *         // the item contains both the image and the caption
 *         array(
 *             'content' => '<img src="http://twitter.github.io/bootstrap/assets/img/bootstrap-mdo-sfmoma-03.jpg"/>',
 *             'caption' => '<h4>This is title</h4><p>This is the caption text</p>',
 *             'options' => array(...),
 *         ),
 *     )
 * ));
 * ```
 * PARAMETERS
 *    auto - auto start carousel sliding (default: true)
 *    period - slide change period (default: 6000)
 *    duration - effect duration period (default: 1000)
 *    effect - animation effect. available: slide, fade, slowdown, switch (default: slide)
 *    direction - animation direction. available: left, right (default: left)
 *    markers - on|off slide markers (default: on)
 *    arrows - on|off slide arrows (default: on)
 *    stop - on|off slide animation on mouse over (default: on)
 *
 *
 */
class Carousel extends Widget
{
	/**
	 * @var array|boolean the labels for the previous and the next control buttons.
	 * If false, it means the previous and the next control buttons should not be displayed.
	 */
	public $controls = array('&lsaquo;', '&rsaquo;');
	/**
	 * @var array list of slides in the carousel. Each array element represents a single
	 * slide with the following structure:
	 *
	 * ```php
	 * array(
	 *     // required, slide content (HTML), such as an image tag
	 *     'content' => '<img src="http://twitter.github.io/bootstrap/assets/img/bootstrap-mdo-sfmoma-01.jpg"/>',
	 *     // optional, the caption (HTML) of the slide
	 *     'caption'=> '<h4>This is title</h4><p>This is the caption text</p>',
	 *     // optional the HTML attributes of the slide container
	 *     'options' => array(),
	 * )
	 * ```
	 */
	public $items = array();


	/**
	 * Initializes the widget.
	 */
	public function init()
	{
		parent::init();
		$this->options=ArrayHelper::merge($this->options,array('data-role'=>'carousel'));
		$this->addCssClass($this->options, 'carousel');
	}

	/**
	 * Renders the widget.
	 */
	public function run()
	{
		echo Html::beginTag('div', $this->options) . "\n";
		echo $this->renderItems() . "\n";
		echo $this->renderControls() . "\n";
		echo Html::endTag('div') . "\n";
		$this->registerPlugin('carousel');
	}

	/**
	 * Renders carousel items as specified on [[items]].
	 * @return string the rendering result
	 */
	public function renderItems()
	{
		$items = array();
		for ($i = 0, $count = count($this->items); $i < $count; $i++) {
			$items[] = $this->renderItem($this->items[$i], $i);
		}
		return Html::tag('div', implode("\n", $items), array('class' => 'slides'));
	}

	/**
	 * Renders a single carousel item
	 * @param string|array $item a single item from [[items]]
	 * @param integer $index the item index as the first item should be set to `active`
	 * @return string the rendering result
	 * @throws InvalidConfigException if the item is invalid
	 */
	public function renderItem($item, $index)
	{
		if (is_string($item)) {
			$content = $item;
			$caption = null;
			$options = array();
		} elseif (isset($item['content'])) {
			$content = $item['content'];
			$caption = ArrayHelper::getValue($item, 'caption');
			if ($caption !== null) {
				$caption = Html::tag('div', $caption, array('class' => 'description'));
			}
			$options = ArrayHelper::getValue($item, 'options', array());
		} else {
			throw new InvalidConfigException('The "content" option is required.');
		}

		$this->addCssClass($options, 'slide image');

		return Html::tag('div', $content . "\n" . $caption, $options);
	}

	/**
	 * Renders previous and next control buttons.
	 * @throws InvalidConfigException if [[controls]] is invalid.
	 */
	public function renderControls()
	{
		if (isset($this->controls[0], $this->controls[1])) {
			return Html::tag('span',$this->controls[0], array(
				'class' => 'control left',
				'id' => '#' . $this->options['id'],				
			)) . "\n"
			. Html::tag('span',$this->controls[1], array(
				'class' => 'control right',
				'id' => '#' . $this->options['id'],				
			));
		} elseif ($this->controls === false) {
			return '';
		} else {
			throw new InvalidConfigException('The "controls" property must be either false or an array of two elements.');
		}
	}
}
