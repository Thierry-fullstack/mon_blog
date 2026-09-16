<?php

namespace App\Form;

use App\Entity\Civility;
use App\Entity\Identity;
use App\Entity\Region;
use App\Repository\RegionRepository;
use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Sequentially;

class IdentityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civility',EntityType::class,['attr'=>['class'=>'form-check form-check-sm'],
                'class'=>Civility::class,
                'choice_label'=>'gender',
                'label'=>'Gender',
                'required'=>true,
                'expanded'=>true,
                'multiple'=>false,
                'constraints'=>([
                    new NotNull()
                ])

            ])
            ->add('skill',TextType::class,['attr'=>['class'=>'form-control form-control-sm'],'label'=>'Skill',
                'required'=>false,
                'constraints'=>([
                    new Sequentially([
                        new Length(max: 255),
                        new Regex(
                            pattern: '/^[a-zA-Z0-9 -\'èçàéïâ]{5,255}$/i',
                            htmlPattern: '^[a-zA-Z0-9 -\'èçàéïâ]{5,255}$',
                            )
                        ])
                    ])
                ])
            ->add('pseudo',TextType::class,['attr'=>['class'=>'form-control form-control-sm'],
                'required'=>true,
                'constraints'=>([
                    new Sequentially([
                        new NotBlank(),
                        new Length(min:3,max:50),
                        new Regex(
                            pattern: '/^[a-zA-Z0-9 -\'èçàéïâ]{3,50}$/i',
                            message: '',
                            htmlPattern: '^[a-zA-Z0-9 -\'èçàéïâ]{3,50}$',
                        )
                    ])
                ])
                ])
            ->add('portrait', FileType::class, ['attr'=>['class'=>'form-control form-control-sm'],
                'multiple'=>false,
                'mapped'=>false,
                'required'=>true,
                'label'=>'Photo',
                'label_attr'=>['class'=>'form-label'],
                'constraints'=>([
                    new Sequentially([
                        new NotBlank(),
                        new File(maxSize: '2M',filenameMaxLength: 2048,extensions: ['jpg','jpeg']),
                      //  new Count(min:1,max: 1),
                        new Image(mimeTypes: ['image/jpeg','image/jpg'],minWidth: 640,minHeight: 480,allowSquare: true,allowLandscape: false,allowPortrait: true)
                    ]),
                ]),
            ])
            ->add('region', EntityType::class, ['attr'=>['class'=>'form-control form-control-sm'],
                'class' => Region::class,
                'query_builder' => function (RegionRepository $er): QueryBuilder {
                return $er->createQueryBuilder('r')
                    ->orderBy('r.name','ASC');
                },
                'placeholder'=>'Choose your region',
                'choice_label' => 'name',
                'required'=>true,
                'constraints'=>([
                    new Sequentially([
                        new NotBlank(),
                        new NotNull()
                    ])
                ]),
            ])
            ->add('submit',SubmitType::class,['attr'=>['class'=>'btn btn-outline-secondary'],'label'=>'Submit your record'])
        ->addEventListener(FormEvents::POST_SUBMIT,$this->addTimer(...))
        ;
    }

    public function addTimer(PostSubmitEvent $event):void
    {
        $data = $event->getData();
        if(!$data instanceof Identity)return;
        $data->setCreatedAt(new DateTimeImmutable());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Identity::class,
        ]);
    }
}
