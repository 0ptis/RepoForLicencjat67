<?php
/**
 * PatchNotes Type
 */
namespace App\Form\Type;

use App\Entity\PatchNotes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

/**
 * PatchNotes Type Class
 */
class PatchNotesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'patch_notes.label.title',
                'attr' => ['placeholder' => 'patch_notes.placeholder.title'],
                'constraints' => [
                    new NotBlank(['message' => 'patch_notes.error.title_empty']),
                    new Length(['min' => 3, 'max' => 255]),
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'patch_notes.label.content',
                'attr' => [
                    'placeholder' => 'patch_notes.placeholder.content',
                    'rows' => 10,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'patch_notes.error.content_empty']),
                ],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PatchNotes::class,
        ]);
    }
}
