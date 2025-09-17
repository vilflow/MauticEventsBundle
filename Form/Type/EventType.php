<?php

namespace MauticPlugin\MauticEventsBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\FormButtonsType;
use MauticPlugin\MauticEventsBundle\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<Event>
 */
class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('eventExternalId', TextType::class, [
            'label'      => 'mautic.events.event_external_id',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'constraints' => [
                new NotBlank(['message' => 'mautic.events.event_external_id.required']),
            ],
        ]);

        $builder->add('name', TextType::class, [
            'label'      => 'mautic.events.name',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'constraints' => [
                new NotBlank(['message' => 'mautic.events.name.required']),
            ],
        ]);

        // URL fields
        $builder->add('eventProgramUrlC', UrlType::class, [
            'label'      => 'Event Program URL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('historyUrlC', UrlType::class, [
            'label'      => 'History URL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('eventSpeakersUrlC', UrlType::class, [
            'label'      => 'Event Speakers URL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('submissionUrlC', UrlType::class, [
            'label'      => 'Submission URL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('eventFaqUrlC', UrlType::class, [
            'label'      => 'Event FAQ URL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('eventVenueUrlC', UrlType::class, [
            'label'      => 'Event Venue URL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('visaUrlC', UrlType::class, [
            'label'      => 'Visa URL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('registrationUrlC', UrlType::class, [
            'label'      => 'Registration URL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('eventFacebookUrlC', UrlType::class, [
            'label'      => 'Event Facebook URL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('eventFeedbackUrlC', UrlType::class, [
            'label'      => 'Event Feedback URL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('eventTestimonialsUrlC', UrlType::class, [
            'label'      => 'Event Testimonials URL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('websiteUrlC', UrlType::class, [
            'label'      => 'Website Url',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('easyPaymentUrlC', UrlType::class, [
            'label'      => 'Easy Payment URL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('declineRedirect', UrlType::class, [
            'label'      => 'Decline Redirect URL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('acceptRedirect', UrlType::class, [
            'label'      => 'Accept Redirect URL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        // Text fields
        $builder->add('eventManagerEmailC', TextType::class, [
            'label'      => 'LBL_EVENT_MANAGER_EMAIL',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('isbnNumberC', TextType::class, [
            'label'      => 'ISBN Number',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        // Text Area fields
        $builder->add('eventWireTransferDataC', TextareaType::class, [
            'label'      => 'Event Wire Transfer Data',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('aboutEventC', TextareaType::class, [
            'label'      => 'About Event',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('description', TextareaType::class, [
            'label'      => 'Description',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        // Integer fields
        $builder->add('durationMinutes', IntegerType::class, [
            'label'      => 'Duration Minutes',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('durationHours', IntegerType::class, [
            'label'      => 'Duration Hours',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        // Image field
        $builder->add('abstractBookImageC', TextType::class, [
            'label'      => 'Abstract Book Image',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        // DateTime fields
        $builder->add('dateEnd', DateTimeType::class, [
            'label'      => 'End Date',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'widget'     => 'single_text',
        ]);

        $builder->add('dateStart', DateTimeType::class, [
            'label'      => 'Start Date',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'widget'     => 'single_text',
        ]);

        // Date fields
        $builder->add('earlyBirdRegDeadlineC', DateType::class, [
            'label'      => 'Early Bird Reg Deadline',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'widget'     => 'single_text',
        ]);

        $builder->add('eventStartDateC', DateType::class, [
            'label'      => 'Event Start Date',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'widget'     => 'single_text',
        ]);

        $builder->add('submissionDeadlineC', DateType::class, [
            'label'      => 'Submission Deadline',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'widget'     => 'single_text',
        ]);

        $builder->add('eventEndDateC', DateType::class, [
            'label'      => 'Event End Date',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'widget'     => 'single_text',
        ]);

        $builder->add('earlyRegDeadlineC', DateType::class, [
            'label'      => 'Early Reg Deadline',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'widget'     => 'single_text',
        ]);

        $builder->add('finalRegDeadlineC', DateType::class, [
            'label'      => 'Final Reg Deadline',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'widget'     => 'single_text',
        ]);

        // Currency and Budget
        $builder->add('currencyId', TextType::class, [
            'label'      => 'Currency',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('budget', NumberType::class, [
            'label'      => 'Budget',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'scale'      => 2,
        ]);

        // Checkbox field
        $builder->add('deleted', CheckboxType::class, [
            'label'      => 'Deleted',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        // Dropdown fields
        $builder->add('eventOrganizerC', ChoiceType::class, [
            'label'      => 'Event Organizer',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'choices'    => [
                'Acavent' => 'Acavent',
                'GlobalKS' => 'GlobalKS',
                'STE' => 'STE',
                'ProudPen' => 'ProudPen',
            ],
            'placeholder' => '-- Select Organizer --',
        ]);

        $builder->add('eventRoundC', ChoiceType::class, [
            'label'      => 'Event Round',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'choices'    => [
                '1st' => '1st',
                '2nd' => '2nd',
                '3rd' => '3rd',
                '4th' => '4th',
                '5th' => '5th',
            ],
            'placeholder' => '-- Select Round --',
        ]);

        $builder->add('eventManagerNameC', ChoiceType::class, [
            'label'      => 'Event Manager Name',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'choices'    => [
                'Romina Dellucci' => 'Romina_Dellucci',
                'Laura Johnson' => 'Laura_Johnson',
            ],
            'placeholder' => '-- Select Manager --',
        ]);

        $builder->add('eventCityC', ChoiceType::class, [
            'label'      => 'Event City',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'choices'    => [
                'Geneva' => 'Geneva',
                'Prague' => 'Prague',
                'Copenhagen' => 'Copenhagen',
                'Berlin' => 'Berlin',
                'Vienna' => 'Vienna',
                'Lisbon' => 'Lisbon',
            ],
            'placeholder' => '-- Select City --',
        ]);

        $builder->add('eventFieldC', ChoiceType::class, [
            'label'      => 'Event Field',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'choices'    => [
                'Education' => 'Education',
                'Social Sciences' => 'Social_Sciences',
                'Management' => 'Management',
            ],
            'placeholder' => '-- Select Field --',
        ]);

        $builder->add('eventCountryC', ChoiceType::class, [
            'label'      => 'Event Country',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'choices'    => [
                'Switzerland' => 'Switzerland',
                'Czech Republic' => 'Czech_Republic',
                'Denmark' => 'Denmark',
                'Germany' => 'Germany',
                'Austria' => 'Austria',
                'Portugal' => 'Portugal',
            ],
            'placeholder' => '-- Select Country --',
        ]);

        $builder->add('activityStatusType', ChoiceType::class, [
            'label'      => 'Activity Status Type',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'choices'    => [
                'Active' => 'active',
                'Inactive' => 'inactive',
            ],
            'placeholder' => '-- Select Status --',
        ]);

        $builder->add('inviteTemplates', ChoiceType::class, [
            'label'      => 'Invite Templates',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
            'choices'    => [
                'Campaign' => 'campaign',
                'Email' => 'email',
                'Event' => 'event',
            ],
            'placeholder' => '-- Select Template --',
        ]);

        $builder->add('buttons', FormButtonsType::class);

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
