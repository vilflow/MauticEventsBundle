<?php

namespace MauticPlugin\MauticEventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<mixed>
 */
class EventDateComparisonConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'firstDate',
            ChoiceType::class,
            [
                'label'       => 'First Date Field',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'choices'     => [
                    'Event Start Date' => 'eventStartDateC',
                    'Event End Date' => 'eventEndDateC',
                    'Submission Deadline' => 'submissionDeadlineC',
                    'Early Bird Reg Deadline' => 'earlyBirdRegDeadlineC',
                    'Early Reg Deadline' => 'earlyRegDeadlineC',
                    'Final Reg Deadline' => 'finalRegDeadlineC',
                    'Date Created' => 'createdAt',
                    'Date Modified' => 'dateModified',
                    'Date Entered' => 'dateEntered',
                ],
                'placeholder' => '-- Select Date Field --',
                'required'    => true,
                'constraints' => [
                    new NotBlank(['message' => 'mautic.core.value.required']),
                ],
            ]
        );

        $builder->add(
            'operator',
            ChoiceType::class,
            [
                'label'       => 'Comparison',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'choices'     => [
                    'is before' => 'lt',
                    'is before or equal to' => 'lte',
                    'is after' => 'gt',
                    'is after or equal to' => 'gte',
                    'is equal to' => 'eq',
                    'is not equal to' => 'neq',
                ],
                'required'    => true,
                'constraints' => [
                    new NotBlank(['message' => 'mautic.core.value.required']),
                ],
            ]
        );

        $builder->add(
            'secondDate',
            ChoiceType::class,
            [
                'label'       => 'Second Date Field',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'choices'     => [
                    'Event Start Date' => 'eventStartDateC',
                    'Event End Date' => 'eventEndDateC',
                    'Submission Deadline' => 'submissionDeadlineC',
                    'Early Bird Reg Deadline' => 'earlyBirdRegDeadlineC',
                    'Early Reg Deadline' => 'earlyRegDeadlineC',
                    'Final Reg Deadline' => 'finalRegDeadlineC',
                    'Date Created' => 'createdAt',
                    'Date Modified' => 'dateModified',
                    'Date Entered' => 'dateEntered',
                ],
                'placeholder' => '-- Select Date Field --',
                'required'    => true,
                'constraints' => [
                    new NotBlank(['message' => 'mautic.core.value.required']),
                ],
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'event_date_comparison';
    }
}
