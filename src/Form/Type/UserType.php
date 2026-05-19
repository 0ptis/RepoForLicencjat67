<?php
/**
 * User Type
 */
namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * User Type Class
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['data'] ?? null;
        $isNewUser = !$user || null === $user->getId();

        $builder
            ->add('email', EmailType::class, [
                'label' => 'form.email',
                'disabled' => !$isNewUser,
            ])
            ->add('nickname', TextType::class, [
                'label' => 'form.nickname',
            ])
            ->add('profilePicture', FileType::class, [
                'label' => 'form.profile_picture',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                        'mimeTypesMessage' => 'form.please.appropriate.image',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => $isNewUser,
                'first_options'  => ['label' => 'form.password.new'],
                'second_options' => ['label' => 'form.password.repeat'],
                'constraints' => array_filter([
                    $isNewUser ? new NotBlank(['message' => 'form.password.required']) : null,
                    new Length(['min' => 6]),
                ]),
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
            'data_class' => User::class,
        ]);
    }
}
