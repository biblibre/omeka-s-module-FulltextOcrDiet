<?php

namespace FulltextDiet;

use Doctrine\Common\Collections\Criteria;
use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\Form\Form;
use Omeka\Form\Element\PropertySelect;
use Omeka\Module\AbstractModule;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach('*', 'api.get_fulltext_text.value_criteria', [$this, 'onApiGetFulltextTextValueCriteria']);
    }

    public function getConfigForm($renderer)
    {
        $services = $this->getServiceLocator();
        $settings = $services->get('Omeka\Settings');
        $selected = $settings->get('fulltextdiet_properties', ['extracttext:extracted_text']);

        $propertySelect = $services->get('FormElementManager')->get(PropertySelect::class);
        $propertySelect->setName('fulltextdiet_properties[]');
        $propertySelect->setOptions([
            'label' => 'Properties to exclude from fulltext search', // @translate
            'info' => 'Selected properties will be excluded from Omeka S fulltext indexation.', // @translate
            'term_as_value' => true,
            'empty_option' => '',
        ]);
        $propertySelect->setAttributes([
            'id' => 'fulltextdiet_properties',
            'class' => 'chosen-select',
            'multiple' => true,
            'value' => $selected,
        ]);

        $form = new Form();
        $form->add($propertySelect);

        return $renderer->formCollection($form, false);
    }

    public function handleConfigForm($controller)
    {
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        $selected = $controller->params()->fromPost('fulltextdiet_properties', []);
        $settings->set('fulltextdiet_properties', array_values(array_filter((array) $selected)));
    }

    public function onApiGetFulltextTextValueCriteria(Event $event)
    {
        $criteria = $event->getParam('criteria');
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        $terms = (array) $settings->get('fulltextdiet_properties', ['extracttext:extracted_text']);
        $terms = array_values(array_filter($terms));

        if (empty($terms)) {
            return;
        }

        $em = $this->getServiceLocator()->get('Omeka\EntityManager');
        $properties = $em->createQuery(
            "SELECT p FROM Omeka\Entity\Property p
             JOIN p.vocabulary v
             WHERE CONCAT(v.prefix, ':', p.localName) IN (:terms)"
        )->setParameter('terms', $terms)->getResult();

        foreach ($properties as $property) {
            $criteria->andWhere(Criteria::expr()->neq('property', $property));
        }
    }
}
