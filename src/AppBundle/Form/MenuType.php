<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 14:51
 */

namespace AppBundle\Form;


use AppBundle\Entity\Agent;
use AppBundle\Entity\Category;
use AppBundle\Entity\Source;
use AppBundle\Entity\Tag;
use AppBundle\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use const true;

class MenuType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $from_default = new \DateTime("first day of this month");
        $to_default = new \DateTime("last day of this month");
        $builder
            ->add('from',DateType::class,[
                'label' => 'From',
                'widget' => 'single_text',
                'html5' => false,
                'format' => "Y-m-d",
                'attr' => [
                    'class' => 'form-control',
                ],
                "required" => false,
                "empty_data" => $from_default->format('Y-m-d'),
            ])
            ->add('to',DateType::class,[
                'label' => 'To',
                'widget' => 'single_text',
                'html5' => false,
                'format' => "Y-m-d",
                'attr' => [
                    'class' => 'form-control',
                ],
                "required" => false,
                "empty_data" => $to_default->format('Y-m-d'),
            ])
            ->add('team',EntityType::class,[
                "label" => "Team",
                "attr" => [
                    "class" => "form-control",
                    "title" => "All",
                    "data-actions-box" => true,
                ],
                "class" => Team::class,
                "choice_label" => "name",
                "required" => false,
                "empty_data" => "all",
                "placeholder" => "All",
                "multiple" => true,
                "expanded" => false,
                "choice_translation_domain" => "tag",
            ])
            ->add('agent',EntityType::class,[
                "label" => "Agent",
                "attr" => [
                    "class" => "form-control",
                    "data-live-search" => true,
                    "title" => "All.agents",
                    "data-actions-box" => true,
                ],
                "class" => Agent::class,
                "choice_label" => "name",
                "required" => false,
                "empty_data" => "all",
                "placeholder" => "All",
                "group_by" => function($val,$key,$index){
                    /** @var Agent $val */
                    return $val->getTeam();
                },
                "multiple" => true,
                "expanded" => false,
                "choice_translation_domain" => "tag",
            ])
            ->add('source',EntityType::class,[
                "label" => "Source",
                "attr" => [
                    "class" => "form-control",
                    "title" => "All",
                    "data-actions-box" => true,
                ],
                "class" => Source::class,
                "choice_label" => "name",
                "required" => false,
                "empty_data" => "all",
                "placeholder" => "All",
                "multiple" => true,
                "expanded" => false,
                "choice_translation_domain" => "tag",
            ])
            ->add('category',EntityType::class,[
                "label" => "Category",
                "attr" => [
                    "class" => "form-control",
                    "title" => "All",
                    "data-actions-box" => true,
                ],
                "class" => Category::class,
                "choice_label" => "name",
                "required" => false,
                "empty_data" => "all",
                "placeholder" => "All",
                "multiple" => true,
                "expanded" => false,
                "choice_translation_domain" => "tag",
            ])
            ->add('tag',EntityType::class,[
                "label" => "Tag",
                "attr" => [
                    "class" => "form-control",
                    "data-live-search" => true,
                    "title" => "All",
                    "data-actions-box" => true,
                ],
                "class" => Tag::class,
                "required" => false,
                "empty_data" => "all",
                "placeholder" => "All",
                "group_by" => function ($val,$key,$index){
                    /** @var Tag $val */
                    return $val->getCategory();
                },
                "multiple" => true,
                "expanded" => false,
                "choice_translation_domain" => "tag",
            ])
            ->add('group',ChoiceType::class,[
                "label" => "Group",
                "attr" => [
                    "class" => "form-control",
                    "title" => "by Days"
                ],
                "choices" => [
                    "by Days" => "days",
                    "by Weeks" => "weeks",
                    "by Months" => "months",
                    "by Years" => "years"
                ],
                "required" => false,
                "empty_data" => "days",
                "choice_translation_domain" => "tag",
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => null,
            "translation_domain" => "tag",
        ]);
    }

}