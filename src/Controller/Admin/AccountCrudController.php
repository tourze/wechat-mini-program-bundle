<?php

namespace WechatMiniProgramBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use WechatMiniProgramBundle\Entity\Account;

/**
 * @extends AbstractCrudController<Account>
 */
#[AdminCrud(routePath: '/wechat-mini-program/account', routeName: 'wechat_mini_program_account')]
final class AccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('微信小程序账户')
            ->setEntityLabelInPlural('微信小程序账户')
            ->setPageTitle('index', '微信小程序账户列表')
            ->setPageTitle('new', '创建微信小程序账户')
            ->setPageTitle('edit', '编辑微信小程序账户')
            ->setPageTitle('detail', '微信小程序账户详情')
            ->setHelp('index', '管理微信小程序的账户配置，包括AppID、AppSecret等信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'appId'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        // 基本字段
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield TextField::new('name', '名称')
            ->setColumns(6)
            ->setHelp('小程序的名称，用于区分不同的小程序账户')
        ;

        yield TextField::new('appId', 'AppID')
            ->setColumns(6)
            ->setHelp('微信小程序的AppID')
        ;

        yield TextField::new('appSecret', 'AppSecret')
            ->setColumns(6)
            ->setHelp('微信小程序的AppSecret，请妥善保管')
            ->onlyOnForms()
        ;

        yield TextField::new('token', 'Token')
            ->setColumns(6)
            ->setHelp('消息推送配置中的Token')
            ->hideOnIndex()
        ;

        yield TextField::new('encodingAesKey', '消息加密密钥')
            ->setColumns(6)
            ->setHelp('消息推送配置中的EncodingAESKey')
            ->hideOnIndex()
        ;

        yield IntegerField::new('loginExpireDay', '登录过期天数')
            ->setColumns(3)
            ->setHelp('用户登录后的有效天数')
            ->hideOnIndex()
        ;

        yield TextField::new('pluginToken', '插件Token')
            ->setColumns(6)
            ->setHelp('插件相关的Token')
            ->hideOnIndex()
        ;

        // 关联字段
        yield AssociationField::new('director', '负责人')
            ->setColumns(6)
            ->setHelp('该小程序账户的负责人')
        ;

        // 状态字段
        yield BooleanField::new('valid', '是否有效')
            ->setColumns(3)
            ->setHelp('控制该账户是否可用')
            ->renderAsSwitch()
        ;

        // 时间戳字段
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
        ;

        // 追踪字段（仅在详情页显示）
        if (Crud::PAGE_DETAIL === $pageName) {
            yield TextField::new('createdBy', '创建人');
            yield TextField::new('updatedBy', '更新人');
            yield TextField::new('createdFromIp', '创建IP');
            yield TextField::new('updatedFromIp', '更新IP');
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '名称'))
            ->add(TextFilter::new('appId', 'AppID'))
            ->add(BooleanFilter::new('valid', '是否有效'))
        ;
    }
}
