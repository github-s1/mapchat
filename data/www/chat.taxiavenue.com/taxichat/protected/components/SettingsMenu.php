<?php
Yii::import('zii.widgets.CMenu');
 
class SettingsMenu extends CMenu
{
    public function renderMenu($items)
    {
        if(count($items))
        {
            echo CHtml::openTag('ul',$this->htmlOptions)."\n";
			echo CHtml::openTag('li',$this->htmlOptions)."\n";
            $this->renderMenuRecursive($items);
           
			echo CHtml::closeTag('li');
			echo CHtml::closeTag('ul');
        }
    }

    public function renderMenuItem($item)
    {
		if(isset($item['url']))
        {
            $label=$this->linkLabelWrapper===null ? $item['label'] : CHtml::tag($this->linkLabelWrapper, $this->linkLabelWrapperHtmlOptions, $item['label']);
			return CHtml::link($label,$item['url'],isset($item['linkOptions']) ? $item['linkOptions'] : array());
        }
        else
            return CHtml::tag('span',isset($item['linkOptions']) ? $item['linkOptions'] : array(), $item['label']);
    }
	
	public function renderMenuItemMy($item, $index, $count)
    {
		//print_r($item); exit;
		$index++;
		$options = isset($item['itemOptions']) ? $item['itemOptions'] : array();
		$class = array();
		if($item['active'] && $this->activeCssClass != '')
			$class[]=$this->activeCssClass;
		
		if($index === 1 && $this->firstItemCssClass!==null)
			$class[]=$this->firstItemCssClass;
		if($index === $count && $this->lastItemCssClass!==null)
			$class[]=$this->lastItemCssClass;
		if($this->itemCssClass!==null)
			$class[]=$this->itemCssClass;
		
		if($class!==array())
		{
			if(empty($options['class']))
				$options['class']=implode(' ',$class);
			else
				$options['class'].=' '.implode(' ',$class);
		}
		$label = $this->linkLabelWrapper===null ? $item['label'] : CHtml::tag($this->linkLabelWrapper, $this->linkLabelWrapperHtmlOptions, $item['label']);
		$getCountModerDrivers = LayoutsInfo::getCountModerDrivers();
		echo CHtml::link($label,$item['url'],$options);	
    }

    public function renderMenuRecursive($items)
    {
 
		$count=0;
        $n = count($items);
		$this->activeCssClass = 'active';
        foreach($items as $index => $item)
        {
			$menu=$this->renderMenuItemMy($item, $index, $n);
			
            if(isset($this->itemTemplate) || isset($item['template']))
            {
                $template=isset($item['template']) ? $item['template'] : $this->itemTemplate;
                echo strtr($template,array('{menu}'=>$menu));
            }
            else
                echo $menu;

            if(isset($item['items']) && count($item['items']))
            {	
				echo(111111);
                echo "\n".CHtml::openTag('ul',isset($item['submenuOptions']) ? $item['submenuOptions'] : $this->submenuHtmlOptions)."\n";
                $this->renderMenuRecursive($item['items']);
                echo CHtml::closeTag('ul')."\n";
            }
            //echo CHtml::closeTag('li')."\n";
			
        }
    }
} ?>
