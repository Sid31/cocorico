<?php

/*
 * This file is part of the Cocorico package.
 *
 * (c) Cocolabs SAS <contact@cocolabs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cocorico\UserBundle\Admin;

use Doctrine\ORM\Query\Expr;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\UserBundle\Admin\Model\UserAdmin as SonataUserAdmin;

class UserAdmin extends SonataUserAdmin
{
    protected $baseRoutePattern = 'user';
    protected $bundles;

    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt'
    );

    public function setBundlesEnabled($bundles)
    {
        $this->bundles = $bundles;
    }

//    protected $perPageOptions = array(5, 15, 25, 50, 100, 150, 200);


    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /* @var $subject \Cocorico\UserBundle\Entity\User */
        $subject = $this->getSubject();

        $formMapper
            ->with('Profile-1')
            ->add(
                'enabled',
                null,
                array(
                    'required' => false,
                )
            )
            ->add(
                'id',
                null,
                array(
                    'required' => true,
                )
            )
            ->add(
                'firstName',
                null,
                array(
                    'required' => true,
                )
            )
            ->add(
                'lastName',
                null,
                array(
                    'required' => true,
                )
            )
            ->add('email')
            ->add(
                'plainPassword',
                'text',
                array(
                    'required' => (!$subject || is_null($subject->getId()))
                )
            )
            ->add(
                'motherTongue',
                'language',
                array(
                    'required' => true,
                )
            )
            ->end();

        $formMapper->with('Profile-2')
            ->add(
                'translations',
                'a2lix_translations',
                array(
                    //'locales' => $this->locales,
//                    'required_locales' => array($this->locale),
                    'fields' => array(
                        'description' => array(
                            'field_type' => 'textarea',
//                            'locale_options' => $descriptions
                        ),
                    ),
                    /** @Ignore */
                    'label' => false
                )
            )
            ->add(
                'birthday',
                'birthday',
                array(
                    'format' => 'dd - MMMM - yyyy',
                    'years' => range(date('Y') - 18, date('Y') - 80),
                )
            )
            ->add(
                'phonePrefix',
                null,
                array(
                    'required' => false,
                )
            )
            ->add(
                'phone',
                null,
                array(
                    'required' => false,
                )
            )
            ->add(
                'nationality',
                'country',
                array(
                    'data' => 'FR'
                )
            )
            ->add(
                'profession',
                null,
                array(
                    'required' => false
                )
            )
            ->add(
                'iban',
                null,
                array(
                    'required' => false
                )
            )
            ->add(
                'bic',
                null,
                array(
                    'required' => false
                )
            )
            ->add(
                'bankOwnerName',
                null,
                array(
                    'required' => false
                )
            )
            ->add(
                'bankOwnerAddress',
                null,
                array(
                    'required' => false
                )
            )
            ->add(
                'annualIncome',
                null,
                array(
                    'required' => false
                )
            )
            ->add(
                'feeAsAsker',//Percent
                'integer',
                array(
                    'attr' => array(
                        'min' => 0,
                        'max' => 100
                    ),
                    'required' => false
                )
            )
            ->add(
                'feeAsOfferer', //Percent
                'integer',
                array(
                    'attr' => array(
                        'min' => 0,
                        'max' => 100
                    ),
                    'required' => false
                )
            )
            ->add(
                'phoneVerified',
                null,
                array(
                    'required' => false
                )
            )
            ->add(
                'emailVerified',
                null,
                array(
                    'required' => false
                )
            )
            ->add(
                'idCardVerified',
                null,
                array(
                    'required' => false
                )
            )
            ->add(
                'nbBookingsOfferer',
                null,
                array(
                    'required' => false,
                    'disabled' => true
                )
            )
            ->add(
                'nbBookingsAsker',
                null,
                array(
                    'required' => false,
                    'disabled' => true
                )
            )
            ->add(
                'createdAt',
                null,
                array(
                    'disabled' => true,
                )
            )
            ->end();

        if (array_key_exists("CocoricoMangoPayBundle", $this->bundles)) {
            $formMapper->with('Mangopay')
                ->add(
                    'mangopayId',
                    null,
                    array(
                        'disabled' => true,
                        'required' => false
                    )
                )
                ->add(
                    'mangopayWalletId',
                    null,
                    array(
                        'disabled' => true,
                        'required' => false
                    )
                )
                ->add(
                    'mangopayBankAccountId',
                    null,
                    array(
                        'disabled' => true,
                        'required' => false
                    )
                )
                ->end();
        }

    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier(
                'id',
                null,
                array()
            );

        if (array_key_exists("CocoricoMangoPayBundle", $this->bundles)) {
            $listMapper->add(
                'mangopayId',
                null,
                array()
            );
        }

        $listMapper
            ->addIdentifier('fullname')
//            ->add('email')
            ->add('enabled', null, array('editable' => true))
            ->add('locked', null, array('editable' => true))
            ->add('feeAsAsker', null, array('editable' => true))
            ->add('feeAsOfferer', null, array('editable' => true))
            ->add('listings', null, array('associated_property' => 'getTitle'))
            ->add(
                'createdAt',
                null,
                array(
                    'format' => "d/m/Y H:i",
                )
            );

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add(
                    'impersonating',
                    'string',
                    array('template' => 'CocoricoSonataAdminBundle::impersonating.html.twig')
                );
        }

        $listMapper->add(
            '_action',
            'actions',
            array(
                'actions' => array(
                    'edit' => array(),
                    'list_user_listings' => array(
                        'template' => 'CocoricoSonataAdminBundle::list_action_list_user_listings.html.twig'
                    )
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('id')
            ->add(
                'fullname',
                'doctrine_orm_callback',
                array(
                    'callback' => array($this, 'getFullNameFilter'),
                    'field_type' => 'text',
                    'operator_type' => 'hidden',
                    'operator_options' => array()
                )
            )
            ->add('locked')
            ->add('email')
            ->add('groups');
    }

    public function getFullNameFilter($queryBuilder, $alias, $field, $value)
    {
        if (!$value['value']) {
            return false;
        }

        $exp = new Expr();
        $queryBuilder
            ->andWhere(
                $exp->orX(
                    $exp->like($alias . '.firstName', $exp->literal('%' . $value['value'] . '%')),
                    $exp->like($alias . '.lastName', $exp->literal('%' . $value['value'] . '%')),
                    $exp->like(
                        $exp->concat(
                            $alias . '.firstName',
                            $exp->concat($exp->literal(' '), $alias . '.lastName')
                        ),
                        $exp->literal('%' . $value['value'] . '%')
                    )
                )
            );

        return true;
    }

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions["delete"]);

        $label = $this->getConfigurationPool()->getContainer()->get('translator')->trans(
            'action_reset_fees',
            array(),
            'SonataAdminBundle'
        );

        $actions['reset_fees'] = array(
            /** @Ignore */
            'label' => $label,
            'ask_confirmation' => true
        );

        return $actions;
    }

    public function getExportFields()
    {
        $fields = array(
            'Id' => 'id',
            'First name' => 'firstName',
            'Last name' => 'lastName',
            'Email' => 'email',
            'Enabled' => 'enabled',
            'Locked' => 'locked',
            'Created At' => 'createdAt'
        );

        if (array_key_exists("CocoricoMangoPayBundle", $this->bundles)) {
            $mangopayFields = array(
                'Mangopay Id' => 'mangopayId',
            );

            $fields = array_merge($fields, $mangopayFields);
        }

        return $fields;
    }

    public function getDataSourceIterator()
    {
        $datagrid = $this->getDatagrid();
        $datagrid->buildPager();

        $dataSourceIt = $this->getModelManager()->getDataSourceIterator($datagrid, $this->getExportFields());
        $dataSourceIt->setDateTimeFormat('d M Y');

        return $dataSourceIt;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete');
    }

}
