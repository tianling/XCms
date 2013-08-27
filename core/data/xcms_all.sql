/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2013/8/26 21:47:09                           */
/*==============================================================*/


drop table if exists xcms_ad_view_click;

drop table if exists xcms_administrators;

drop table if exists xcms_advertise;

drop table if exists xcms_advertise_pic;

drop table if exists xcms_advertiser;

drop table if exists xcms_api_group;

drop table if exists xcms_api_role;

drop table if exists xcms_api_user;

drop table if exists xcms_area;

drop table if exists xcms_auth_gr;

drop table if exists xcms_auth_groups;

drop table if exists xcms_auth_mutex;

drop table if exists xcms_auth_operation;

drop table if exists xcms_auth_permission;

drop table if exists xcms_auth_protected_table;

drop table if exists xcms_auth_resource;

drop table if exists xcms_auth_resource_type;

drop table if exists xcms_auth_role_permission;

drop table if exists xcms_auth_roles;

drop table if exists xcms_auth_user_permission;

drop table if exists xcms_chat_admin;

drop table if exists xcms_chat_message;

drop table if exists xcms_chat_pic;

drop table if exists xcms_chat_room;

drop table if exists xcms_chat_shielded;

drop table if exists xcms_community;

drop table if exists xcms_community_user;

drop table if exists xcms_group_admin;

drop table if exists xcms_group_message;

drop table if exists xcms_group_msg_pic;

drop table if exists xcms_group_shielded;

drop table if exists xcms_groups;

drop table if exists xcms_offline_chat_message;

drop table if exists xcms_offline_group_message;

drop table if exists xcms_pay_receipt;

drop table if exists xcms_pay_to;

drop table if exists xcms_property;

drop table if exists xcms_property_community;

drop table if exists xcms_property_push;

drop table if exists xcms_property_push_receiver;

drop table if exists xcms_setting;

drop table if exists xcms_sqb_user;

drop table if exists xcms_user;

drop table if exists xcms_user_address;

drop table if exists xcms_user_blacklist;

drop table if exists xcms_user_contacts;

drop table if exists xcms_user_group;

drop table if exists xcms_user_icon;

drop table if exists xcms_user_interest;

drop table if exists xcms_user_message;

drop table if exists xcms_user_message_pic;

drop table if exists xcms_user_own_chat;

drop table if exists xcms_user_own_group;

drop table if exists xcms_user_pay;

drop table if exists xcms_user_report;

drop table if exists xcms_user_role;

drop table if exists xcms_user_trends;

drop table if exists xcms_user_trends_pic;

drop table if exists xcms_user_trends_reply;

drop table if exists xcms_user_trends_support;

/*==============================================================*/
/* Table: xcms_advertiser                                       */
/*==============================================================*/
create table xcms_advertiser
(
   advertiser_id        int(11) unsigned not null comment '广告主其他信息来自管理员信息，如密码',
   balance              double(11,0) not null default 0 comment '广告主余额',
   phone                varchar(11) default NULL comment '广告主手机，不能重复，可以为空',
   primary key (advertiser_id)
);

alter table xcms_advertiser comment '广告主表';

/*==============================================================*/
/* Table: xcms_advertise                                        */
/*==============================================================*/
create table xcms_advertise
(
   id                   int(11) unsigned not null auto_increment,
   advertiser_id        int(11) unsigned not null,
   title                varchar(20) not null,
   content              text not null,
   view                 int(11) not null default 0,
   click                int(11) not null default 0,
   direct_to            varchar(255) not null,
   pay_type             tinyint(1) unsigned not null default 1 comment '0-cpm,1-cpc，默认为cpc',
   cpm                  double(11,0) not null default 0,
   cpc                  double(11,0) not null default 0,
   priority             tinyint(1) not null default 0 comment '0-低，1-中，2,-高，3-非常高',
   primary key (id),
   constraint FK_AD_ADER_ADER foreign key (advertiser_id)
      references xcms_advertiser (advertiser_id) on delete cascade on update cascade
);

alter table xcms_advertise comment '广告内容表';

/*==============================================================*/
/* Table: xcms_user                                             */
/*==============================================================*/
create table xcms_user
(
   id                   int(11) unsigned not null auto_increment,
   nickname             varchar(20) not null comment '用户昵称，不能重复，可用于登录',
   realname             varchar(5),
   email                varchar(50) comment '用户邮箱，可用于登录，可以为空',
   password             varchar(255) not null,
   salt                 varchar(128),
   last_login_time      int(11) unsigned not null,
   last_login_ip        varchar(15) not null,
   locked               tinyint(1) not null default 0 comment '0为没锁定，1为锁定，默认为0',
   primary key (id)
);

alter table xcms_user comment '网站用户表';

/*==============================================================*/
/* Table: xcms_ad_view_click                                    */
/*==============================================================*/
create table xcms_ad_view_click
(
   user_id              int(11) unsigned not null,
   advertise_id         int(11) unsigned not null,
   type                 tinyint(2) unsigned not null comment '是点击还是浏览。0-浏览，1-点击',
   primary key (user_id, advertise_id, type),
   constraint FK_USER_AD_VIEW_CLICK_AD foreign key (advertise_id)
      references xcms_advertise (id) on update cascade,
   constraint FK_USER_AD_VIEW_CLICK_U foreign key (user_id)
      references xcms_user (id) on update cascade
);

alter table xcms_ad_view_click comment '用户浏览点击表';

/*==============================================================*/
/* Table: xcms_administrators                                   */
/*==============================================================*/
create table xcms_administrators
(
   id                   int(11) unsigned not null,
   surname              varchar(10) default NULL comment '姓氏，默认为空',
   name                 varchar(10) default NULL comment '姓名，可以为空',
   primary key (id),
   constraint FK_ADMIN_USER_U foreign key (id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_administrators comment '一种类型的用户信息表。存放管理员信息。';

/*==============================================================*/
/* Table: xcms_advertise_pic                                    */
/*==============================================================*/
create table xcms_advertise_pic
(
   ad_id                int(11) unsigned not null,
   url                  varchar(255) not null,
   description          text default NULL,
   primary key (ad_id),
   constraint FK_AD_PIC_P foreign key (ad_id)
      references xcms_advertise (id) on delete cascade on update cascade
);

alter table xcms_advertise_pic comment '广告图片表';

/*==============================================================*/
/* Index: adver_phone                                           */
/*==============================================================*/
create index adver_phone on xcms_advertiser
(
   phone
);

/*==============================================================*/
/* Table: xcms_api_user                                         */
/*==============================================================*/
create table xcms_api_user
(
   id                   int(11) unsigned not null auto_increment,
   username             varchar(30) not null comment '用户名称不能重复',
   token                varchar(40) not null comment '用户token，sha1加密',
   salt                 varchar(20) not null comment '密码盐',
   primary key (id)
);

alter table xcms_api_user comment 'API用户';

/*==============================================================*/
/* Table: xcms_auth_groups                                      */
/*==============================================================*/
create table xcms_auth_groups
(
   id                   int(11) unsigned not null auto_increment,
   group_name           varchar(30) not null comment '用户组名称',
   description          text comment '用户组描述',
   enabled              tinyint(1) unsigned not null default 0 comment '是否禁止使用，0代表开始，1代表禁止，默认为0',
   list_order           int(5) unsigned not null default 0 comment '显示顺序，默认为0',
   primary key (id)
);

alter table xcms_auth_groups comment '存放用户组信息';

/*==============================================================*/
/* Table: xcms_api_group                                        */
/*==============================================================*/
create table xcms_api_group
(
   group_id             int(11) unsigned not null,
   user_id              int(11) unsigned not null,
   primary key (group_id, user_id),
   constraint FK_APIUSER_GROUP_U foreign key (user_id)
      references xcms_api_user (id) on delete cascade on update cascade,
   constraint FK_APIUSER_GROUP_G foreign key (group_id)
      references xcms_auth_groups (id) on delete cascade on update cascade
);

alter table xcms_api_group comment 'API用户用户组关联表';

/*==============================================================*/
/* Table: xcms_auth_roles                                       */
/*==============================================================*/
create table xcms_auth_roles
(
   id                   int(11) unsigned not null auto_increment,
   fid                  int(11) unsigned not null,
   level                int(11) unsigned not null,
   lft                  int(11) unsigned not null,
   rgt                  int(11) unsigned not null,
   role_name            varchar(30) not null comment '角色名称',
   description          text comment '角色描述',
   enabled              tinyint(1) unsigned not null default 0 comment '是否禁止使用，0表示开启，1表示关闭，默认为0',
   list_order           int(5) unsigned not null default 0 comment '显示顺序，默认为0',
   primary key (id)
);

/*==============================================================*/
/* Table: xcms_api_role                                         */
/*==============================================================*/
create table xcms_api_role
(
   role_id              int(11) unsigned not null,
   user_id              int(11) unsigned not null,
   primary key (role_id, user_id),
   constraint FK_APIUSER_ROLE_U foreign key (user_id)
      references xcms_api_user (id) on delete restrict on update restrict,
   constraint FK_APIUSER_ROLE_R foreign key (role_id)
      references xcms_auth_roles (id) on delete cascade on update cascade
);

alter table xcms_api_role comment 'API用户角色关联表';

/*==============================================================*/
/* Index: unique_api_user                                       */
/*==============================================================*/
create unique index unique_api_user on xcms_api_user
(
   username
);

/*==============================================================*/
/* Table: xcms_area                                             */
/*==============================================================*/
create table xcms_area
(
   id                   int(11) unsigned not null auto_increment,
   fid                  int(11) unsigned not null,
   area_name            varchar(10) not null,
   primary key (id)
);

alter table xcms_area comment '地区';

/*==============================================================*/
/* Table: xcms_auth_gr                                          */
/*==============================================================*/
create table xcms_auth_gr
(
   role_id              int(11) unsigned not null,
   group_id             int(11) unsigned not null,
   primary key (role_id, group_id),
   constraint FK_GROUP_ROLE_G foreign key (group_id)
      references xcms_auth_groups (id) on delete cascade on update cascade,
   constraint FK_GROUP_ROLE_R foreign key (role_id)
      references xcms_auth_roles (id) on delete cascade on update cascade
);

alter table xcms_auth_gr comment '关联用户组和角色';

/*==============================================================*/
/* Table: xcms_auth_mutex                                       */
/*==============================================================*/
create table xcms_auth_mutex
(
   role_one             int(11) unsigned not null,
   role_two             int(11) unsigned not null,
   description          text,
   primary key (role_one, role_two),
   constraint FK_ROLE_MUTEX_ONE foreign key (role_one)
      references xcms_auth_roles (id) on delete cascade on update cascade,
   constraint FK_ROLE_MUTEX_TWO foreign key (role_two)
      references xcms_auth_roles (id) on delete cascade on update cascade
);

alter table xcms_auth_mutex comment '角色互斥';

/*==============================================================*/
/* Table: xcms_auth_operation                                   */
/*==============================================================*/
create table xcms_auth_operation
(
   id                   int(11) unsigned not null auto_increment,
   operation_name       varchar(20) not null,
   description          text,
   module               varchar(30),
   controller           varchar(30) not null,
   action               varchar(30) not null,
   primary key (id)
);

/*==============================================================*/
/* Index: unique_module_controller_action                       */
/*==============================================================*/
create unique index unique_module_controller_action on xcms_auth_operation
(
   module,
   controller,
   action
);

/*==============================================================*/
/* Table: xcms_auth_resource_type                               */
/*==============================================================*/
create table xcms_auth_resource_type
(
   id                   int(11) unsigned not null auto_increment,
   type_name            varchar(20) not null,
   description          text,
   table_name           varchar(40) not null,
   primary key (id)
);

/*==============================================================*/
/* Table: xcms_auth_resource                                    */
/*==============================================================*/
create table xcms_auth_resource
(
   id                   int(11) unsigned not null auto_increment,
   type_id              int(11) unsigned not null,
   resource_name        varchar(30) not null,
   description          text,
   primary key (id),
   constraint FK_RESOURCE_TYPE_T foreign key (type_id)
      references xcms_auth_resource_type (id) on delete cascade on update cascade
);

/*==============================================================*/
/* Table: xcms_auth_permission                                  */
/*==============================================================*/
create table xcms_auth_permission
(
   id                   int(11) unsigned not null auto_increment,
   function_id          int(11) unsigned not null,
   resource_id          int(11) unsigned default NULL comment '权限管理的资源，可以为NULL',
   permission_name      varchar(20) not null,
   description          text default NULL comment '权限描述，默认为空',
   primary key (id),
   constraint FK_PERMISSION_OPERATION_RESOURCE_O foreign key (function_id)
      references xcms_auth_operation (id) on delete cascade on update cascade,
   constraint FK_PERMISSION_OPERATION_RESOURCE_R foreign key (resource_id)
      references xcms_auth_resource (id) on delete cascade on update cascade
);

/*==============================================================*/
/* Index: unique_operation_resource                             */
/*==============================================================*/
create unique index unique_operation_resource on xcms_auth_permission
(
   resource_id,
   function_id
);

/*==============================================================*/
/* Table: xcms_auth_protected_table                             */
/*==============================================================*/
create table xcms_auth_protected_table
(
   id                   int(11) unsigned not null auto_increment,
   resource_type        int(11) unsigned not null,
   table_name           varchar(30) not null,
   field_name           varchar(20) default NULL comment '受控数据表内字段，可为空',
   description          text,
   primary key (id),
   constraint FK_PROTECTED_RESOURCE_TYPE_T foreign key (resource_type)
      references xcms_auth_resource_type (id) on delete cascade on update cascade
);

alter table xcms_auth_protected_table comment '存储保护下的数据表';

/*==============================================================*/
/* Index: unique_table_field_resource_type                      */
/*==============================================================*/
create unique index unique_table_field_resource_type on xcms_auth_protected_table
(
   table_name,
   field_name,
   resource_type
);

/*==============================================================*/
/* Index: resource_type_name                                    */
/*==============================================================*/
create index resource_type_name on xcms_auth_resource_type
(
   table_name
);

/*==============================================================*/
/* Table: xcms_auth_role_permission                             */
/*==============================================================*/
create table xcms_auth_role_permission
(
   role_id              int(11) unsigned not null,
   permission_id        int(11) unsigned not null,
   primary key (role_id, permission_id),
   constraint FK_ROLE_PERMISSION_R foreign key (role_id)
      references xcms_auth_roles (id) on delete cascade on update cascade,
   constraint FK_ROLE_PERMISSION_P foreign key (permission_id)
      references xcms_auth_permission (id) on delete cascade on update cascade
);

alter table xcms_auth_role_permission comment '关联角色和权限';

/*==============================================================*/
/* Index: role_list_order                                       */
/*==============================================================*/
create index role_list_order on xcms_auth_roles
(
   list_order
);

/*==============================================================*/
/* Table: xcms_auth_user_permission                             */
/*==============================================================*/
create table xcms_auth_user_permission
(
   permission_id        int(11) unsigned not null,
   user_id              int(11) unsigned not null,
   is_own               tinyint(1) not null comment '用户是否具有这个权限，1代表有，-1代表没有。在权限计算时从总权限中加上或减去这个权限',
   expire               int(11) unsigned not null,
   primary key (permission_id, user_id),
   constraint FK_USER_PERMISSION_U foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade,
   constraint FK_USER_PERMISSION_P foreign key (permission_id)
      references xcms_auth_permission (id) on delete restrict on update restrict
);

alter table xcms_auth_user_permission comment '为用户直接赋予或取消某些权限';

/*==============================================================*/
/* Table: xcms_chat_room                                        */
/*==============================================================*/
create table xcms_chat_room
(
   id                   int(11) unsigned not null auto_increment,
   room_name            varchar(15) not null,
   user_num             int(5) not null default 0 comment '聊天室人数，默认为0',
   description          text,
   admin_num            tinyint(2) not null comment '管理员数量，默认为0',
   primary key (id)
);

alter table xcms_chat_room comment '小区聊天室';

/*==============================================================*/
/* Table: xcms_chat_admin                                       */
/*==============================================================*/
create table xcms_chat_admin
(
   room_id              int(11) unsigned not null,
   user_id              int(11) unsigned not null,
   primary key (room_id, user_id),
   constraint FK_CHAT_ADMIN_C foreign key (room_id)
      references xcms_chat_room (id) on delete cascade on update cascade,
   constraint FK_CHAT_ADMIN_A foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_chat_admin comment '聊天室管理员关联表';

/*==============================================================*/
/* Table: xcms_chat_message                                     */
/*==============================================================*/
create table xcms_chat_message
(
   id                   int(11) unsigned not null auto_increment,
   sender               int(11) unsigned not null,
   receive_room         int(11) unsigned not null,
   content              text not null,
   send_time            int(11) unsigned not null,
   status               tinyint(1) unsigned not null comment '0-成功，1-失败，2-正在发送',
   primary key (id),
   constraint FK_CHAT_MESSAGE_SENDER foreign key (sender)
      references xcms_user (id) on delete cascade on update cascade,
   constraint FK_CHAT_MESSAGE_ROOM foreign key (receive_room)
      references xcms_chat_room (id) on delete cascade on update cascade
);

alter table xcms_chat_message comment '聊天室消息';

/*==============================================================*/
/* Table: xcms_chat_pic                                         */
/*==============================================================*/
create table xcms_chat_pic
(
   id                   int(11) unsigned not null auto_increment,
   msg_id               int(11) unsigned not null,
   url                  varchar(255) not null,
   primary key (id),
   constraint FK_CHAT_PIC_MSG foreign key (msg_id)
      references xcms_chat_message (id) on delete cascade on update cascade
);

alter table xcms_chat_pic comment '聊天室消息图片';

/*==============================================================*/
/* Table: xcms_chat_shielded                                    */
/*==============================================================*/
create table xcms_chat_shielded
(
   room_id              int(11) unsigned not null,
   user_id              int(11) unsigned not null,
   primary key (room_id, user_id),
   constraint FK_USER_CHAT_SHIELDED_C foreign key (room_id)
      references xcms_chat_room (id) on delete cascade on update cascade,
   constraint FK_USER_CHAT_SHIELDED_U foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_chat_shielded comment '用户屏蔽聊天室消息';

/*==============================================================*/
/* Table: xcms_community                                        */
/*==============================================================*/
create table xcms_community
(
   id                   int(11) unsigned not null auto_increment,
   community_name       varchar(15) not null,
   primary key (id),
   constraint FK_COMMUNITY_GROUP_G foreign key (id)
      references xcms_auth_groups (id) on delete cascade on update cascade
);

alter table xcms_community comment '小区';

/*==============================================================*/
/* Table: xcms_sqb_user                                         */
/*==============================================================*/
create table xcms_sqb_user
(
   id                   int(11) unsigned not null,
   identity_id          varchar(18) not null comment '身份证号，可用于登录，唯一',
   gender               tinyint(1) unsigned not null comment '0-女，1-男，2-不男不女',
   mobile               varchar(11) not null comment '手机号，不能重复',
   phone                varchar(20),
   groups               int(11) unsigned not null default 0 comment '拥有的群数量，默认为0',
   attention            int(11) unsigned not null default 0 comment '关注数量，默认为0',
   be_concerned         int(11) unsigned not null default 0 comment '被关注数量，默认为0',
   online_status        tinyint(2) not null default 0 comment '0离线，1在线，还可以定义更多，默认为0',
   primary key (id),
   constraint FK_SqbUSER_U foreign key (id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_sqb_user comment '社区宝用户表';

/*==============================================================*/
/* Table: xcms_community_user                                   */
/*==============================================================*/
create table xcms_community_user
(
   property_id          int(11) unsigned not null,
   user_id              int(11) unsigned not null,
   primary key (property_id, user_id),
   constraint FK_USER_COMMUNITY_C foreign key (property_id)
      references xcms_community (id) on delete cascade on update cascade,
   constraint FK_USER_COMMUNITY_U foreign key (user_id)
      references xcms_sqb_user (id) on delete cascade on update cascade
);

/*==============================================================*/
/* Table: xcms_groups                                           */
/*==============================================================*/
create table xcms_groups
(
   id                   int(11) unsigned not null,
   master_id            int(11) unsigned not null,
   group_name           varchar(15) not null,
   description          text,
   announcement         tinytext,
   admin_num            tinyint(2) not null comment '管理员人数，默认为0',
   user_num             int(4) not null default 1 comment '群人数，默认为1',
   creation_time        int(11) not null,
   primary key (id),
   constraint FK_USER_GROUP_MASTER foreign key (master_id)
      references xcms_user (id) on update cascade
);

alter table xcms_groups comment '用户所创建的群';

/*==============================================================*/
/* Table: xcms_group_admin                                      */
/*==============================================================*/
create table xcms_group_admin
(
   group_id             int(11) unsigned not null,
   user_id              int(11) unsigned not null,
   primary key (group_id, user_id),
   constraint FK_GROUP_ADMIN_G foreign key (group_id)
      references xcms_groups (id) on delete restrict on update restrict,
   constraint FK_GROUP_ADMIN_A foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_group_admin comment '群管理员用户关联表';

/*==============================================================*/
/* Table: xcms_group_message                                    */
/*==============================================================*/
create table xcms_group_message
(
   id                   int(11) unsigned not null auto_increment,
   sender               int(11) unsigned not null,
   receive_group        int(11) unsigned not null,
   content              text not null,
   send_time            int(11) unsigned not null,
   status               tinyint(1) unsigned not null comment '0-成功，1-失败，2-正在发送',
   primary key (id),
   constraint FK_GROUP_MSG_SENDER foreign key (sender)
      references xcms_user (id) on delete cascade on update cascade,
   constraint FK_GROUP_MSG_G foreign key (receive_group)
      references xcms_groups (id) on delete cascade on update cascade
);

alter table xcms_group_message comment '群消息';

/*==============================================================*/
/* Table: xcms_group_msg_pic                                    */
/*==============================================================*/
create table xcms_group_msg_pic
(
   id                   int(11) unsigned not null auto_increment,
   msg_id               int(11) unsigned not null,
   url                  varchar(255) not null,
   primary key (id),
   constraint FK_GROUPMSG_PIC_MSG foreign key (msg_id)
      references xcms_group_message (id) on delete cascade on update cascade
);

alter table xcms_group_msg_pic comment '群消息图片';

/*==============================================================*/
/* Table: xcms_group_shielded                                   */
/*==============================================================*/
create table xcms_group_shielded
(
   group_id             int(11) unsigned not null,
   user_id              int(11) unsigned not null,
   primary key (group_id, user_id),
   constraint FK_USER_GROUP_SHIELDED_G foreign key (group_id)
      references xcms_groups (id) on delete cascade on update cascade,
   constraint FK_USER_GROUP_SHIELDED_U foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_group_shielded comment '用户屏蔽群消息，屏蔽时依然存储消息，但是不发送到用户';

/*==============================================================*/
/* Table: xcms_offline_chat_message                             */
/*==============================================================*/
create table xcms_offline_chat_message
(
   msg_id               int(11) unsigned not null,
   user_id              int(11) unsigned not null,
   primary key (msg_id, user_id),
   constraint FK_OFFLINE_CHAT_MSG_M foreign key (msg_id)
      references xcms_chat_message (id) on delete cascade on update cascade,
   constraint FK_OFFLINE_CHAT_MSG_U foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_offline_chat_message comment '离线聊天室消息';

/*==============================================================*/
/* Table: xcms_offline_group_message                            */
/*==============================================================*/
create table xcms_offline_group_message
(
   msg_id               int(11) unsigned not null,
   user_id              int(11) unsigned not null,
   primary key (msg_id, user_id),
   constraint FK_OFFLINE_GROUP_MSG_M foreign key (msg_id)
      references xcms_group_message (id) on delete cascade on update cascade,
   constraint FK_OFFLINE_GROUP_MSG_U foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_offline_group_message comment '离线群消息';

/*==============================================================*/
/* Table: xcms_pay_receipt                                      */
/*==============================================================*/
create table xcms_pay_receipt
(
   id                   int(11) unsigned not null auto_increment,
   period               varchar(25) not null,
   deadline             int(11) unsigned comment '可空，提示用户最后缴费日期，过了此期间可能会产生滞纳金',
   start                varchar(15) comment '根据类型不同会不一样',
   stop                 varchar(15),
   used                 varchar(15) comment '单位根据不同的类型不同，如水为吨，气为方，电为度数，物管费为平方米',
   money                varchar(15) comment '本月产生的费用',
   old_money            varchar(15) comment '为负表示上期还欠',
   liquidated           varchar(15) comment '长时间不缴费产生的费用',
   should_pay           varchar(15) comment '实际应该缴费多少，为金额+附加费+违约金-上期余额',
   barcode              varchar(255),
   description          tinytext,
   primary key (id)
);

alter table xcms_pay_receipt comment '缴费回执信息';

/*==============================================================*/
/* Table: xcms_pay_to                                           */
/*==============================================================*/
create table xcms_pay_to
(
   id                   int(11) unsigned not null auto_increment,
   company_name         varchar(30) not null,
   description          text,
   primary key (id)
);

/*==============================================================*/
/* Table: xcms_property                                         */
/*==============================================================*/
create table xcms_property
(
   id                   int(11) unsigned not null auto_increment,
   property_name        varchar(15) not null comment '物管名称',
   primary key (id)
);

/*==============================================================*/
/* Index: property_name                                         */
/*==============================================================*/
create index property_name on xcms_property
(
   property_name
);

/*==============================================================*/
/* Table: xcms_property_community                               */
/*==============================================================*/
create table xcms_property_community
(
   property_id          int(11) unsigned not null,
   community_id         int(11) unsigned not null,
   primary key (property_id, community_id),
   constraint FK_PROPERTY_COMMUNITY_P foreign key (property_id)
      references xcms_property (id) on delete cascade on update cascade,
   constraint FK_PROPERTY_COMMUNITY_C foreign key (community_id)
      references xcms_community (id) on delete cascade on update cascade
);

/*==============================================================*/
/* Table: xcms_property_push                                    */
/*==============================================================*/
create table xcms_property_push
(
   id                   int(11) unsigned not null auto_increment,
   property_id          int(11) unsigned not null,
   title                varchar(30) not null,
   content              text not null,
   send_time            int(11) unsigned not null,
   push_type            tinyint(1) unsigned not null comment '0-推送到用户组，1-推送到用户',
   status               tinyint(1) unsigned not null comment '0-失败，1-完成，2-正在推送',
   primary key (id),
   constraint FK_PROPERTY_PUSH_PROPERTY foreign key (property_id)
      references xcms_property (id) on delete cascade on update cascade
);

alter table xcms_property_push comment '物管推送消息';

/*==============================================================*/
/* Table: xcms_property_push_receiver                           */
/*==============================================================*/
create table xcms_property_push_receiver
(
   id                   int(11) unsigned not null auto_increment,
   msg_id               int(11) unsigned not null,
   receiver_id          int(11) unsigned not null,
   primary key (id),
   constraint FK_PROPERTY_PUSH_RECEVIER_R foreign key (id)
      references xcms_property_push (id) on delete cascade on update cascade
);

alter table xcms_property_push_receiver comment '物管消息接收者';

/*==============================================================*/
/* Table: xcms_setting                                          */
/*==============================================================*/
create table xcms_setting
(
   id                   int(11) unsigned not null auto_increment,
   setting_key          varchar(30) not null comment '可重复，代表数组',
   value                varchar(30) not null,
   primary key (id)
);

alter table xcms_setting comment '网站设置';

/*==============================================================*/
/* Index: config_name                                           */
/*==============================================================*/
create unique index config_name on xcms_setting
(
   setting_key
);

/*==============================================================*/
/* Index: unique_user_mobile                                    */
/*==============================================================*/
create unique index unique_user_mobile on xcms_sqb_user
(
   mobile
);

/*==============================================================*/
/* Index: unique_identity_id                                    */
/*==============================================================*/
create unique index unique_identity_id on xcms_sqb_user
(
   identity_id
);

/*==============================================================*/
/* Index: unique_nickname                                       */
/*==============================================================*/
create unique index unique_nickname on xcms_user
(
   nickname
);

/*==============================================================*/
/* Table: xcms_user_address                                     */
/*==============================================================*/
create table xcms_user_address
(
   id                   int(11) unsigned not null auto_increment,
   user_id              int(11) unsigned not null,
   province             int(11) unsigned not null,
   city                 int(11) unsigned not null,
   address              varchar(255),
   community            int(11) unsigned not null,
   building             int(11) unsigned default NULL,
   property             int(11) unsigned default NULL comment '因为可以留空，所以不设置外键',
   room                 varchar(10),
   help_mark            varchar(20),
   contact_phone        varchar(15) not null,
   hosehold             varchar(5) not null,
   primary key (id),
   constraint FK_USER_ADDRESS_U foreign key (user_id)
      references xcms_sqb_user (id) on delete cascade on update cascade,
   constraint FK_AREA_PROVINCE foreign key (province)
      references xcms_area (id) on update cascade,
   constraint FK_AREA_CITY foreign key (city)
      references xcms_area (id) on update cascade
);

alter table xcms_user_address comment '用户住址';

/*==============================================================*/
/* Table: xcms_user_blacklist                                   */
/*==============================================================*/
create table xcms_user_blacklist
(
   user_id              int(11) unsigned not null,
   black_user_id        int(11) unsigned not null,
   primary key (user_id, black_user_id),
   constraint FK_USER_BLACKLIST_U foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade,
   constraint FK_USER_BLACKLIST_BLACK_USER foreign key (black_user_id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_user_blacklist comment '用户黑名单';

/*==============================================================*/
/* Table: xcms_user_contacts                                    */
/*==============================================================*/
create table xcms_user_contacts
(
   id                   int(11) unsigned not null auto_increment,
   user_id              int(11) unsigned not null,
   contact_phone        varchar(11),
   contact_name         varchar(8),
   primary key (id),
   constraint FK_USER_CONTACTS_U foreign key (user_id)
      references xcms_sqb_user (id) on update cascade
);

alter table xcms_user_contacts comment '用户通讯录';

/*==============================================================*/
/* Index: unique_user_phone_name                                */
/*==============================================================*/
create unique index unique_user_phone_name on xcms_user_contacts
(
   user_id,
   contact_phone,
   contact_name
);

/*==============================================================*/
/* Table: xcms_user_group                                       */
/*==============================================================*/
create table xcms_user_group
(
   user_id              int(11) unsigned not null,
   group_id             int(11) unsigned not null,
   primary key (user_id, group_id),
   constraint FK_GROUP_USER_U foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade,
   constraint FK_GROUP_USER_G foreign key (group_id)
      references xcms_auth_groups (id) on delete cascade on update cascade
);

alter table xcms_user_group comment '用户用户组关联表';

/*==============================================================*/
/* Table: xcms_user_icon                                        */
/*==============================================================*/
create table xcms_user_icon
(
   id                   int(11) unsigned not null auto_increment,
   user_id              int(11) unsigned not null,
   url                  varchar(255) not null,
   is_using             tinyint(1) unsigned not null comment '0-没有使用，1-正在使用',
   primary key (id),
   constraint FK_USER_ICON_U foreign key (user_id)
      references xcms_sqb_user (id) on delete cascade on update cascade
);

alter table xcms_user_icon comment '用户头像';

/*==============================================================*/
/* Table: xcms_user_interest                                    */
/*==============================================================*/
create table xcms_user_interest
(
   follower             int(11) unsigned not null,
   followed             int(11) unsigned not null,
   remark               varchar(10) default NULL,
   primary key (follower, followed),
   constraint FK_USER_INTEREST_FROM foreign key (follower)
      references xcms_user (id) on delete cascade on update cascade,
   constraint FK_USER_INTEREST_TO foreign key (followed)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_user_interest comment '用户相互关注';

/*==============================================================*/
/* Table: xcms_user_message                                     */
/*==============================================================*/
create table xcms_user_message
(
   id                   int(11) unsigned not null auto_increment,
   sender               int(11) unsigned not null,
   receiver             int(11) unsigned not null,
   content              text not null,
   send_time            int(11) unsigned not null,
   status               tinyint(1) unsigned not null comment '0-成功，1-失败，2-正在发送',
   primary key (id)
);

alter table xcms_user_message comment '用户之间消息';

/*==============================================================*/
/* Table: xcms_user_message_pic                                 */
/*==============================================================*/
create table xcms_user_message_pic
(
   id                   int(11) unsigned not null auto_increment,
   msg_id               int(11) unsigned not null,
   url                  varchar(255) not null,
   primary key (id),
   constraint FK_USER_MESSAGE_M foreign key (msg_id)
      references xcms_user_message (id) on delete cascade on update cascade
);

alter table xcms_user_message_pic comment '用户间消息图片';

/*==============================================================*/
/* Table: xcms_user_own_chat                                    */
/*==============================================================*/
create table xcms_user_own_chat
(
   room_id              int(11) unsigned not null,
   user_id              int(11) unsigned not null,
   remark               varchar(15) default NULL,
   primary key (room_id, user_id),
   constraint FK_USER_CHAT_C foreign key (room_id)
      references xcms_chat_room (id) on delete cascade on update cascade,
   constraint FK_USER_CHAT_U foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_user_own_chat comment '用户所属聊天室';

/*==============================================================*/
/* Table: xcms_user_own_group                                   */
/*==============================================================*/
create table xcms_user_own_group
(
   group_id             int(11) unsigned not null,
   user_id              int(11) unsigned not null,
   remark               varchar(15) default NULL,
   primary key (group_id, user_id),
   constraint FK_USER_GROUP_G foreign key (group_id)
      references xcms_groups (id) on delete cascade on update cascade,
   constraint FK_USER_GROUP_U foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_user_own_group comment '用户所属群';

/*==============================================================*/
/* Table: xcms_user_pay                                         */
/*==============================================================*/
create table xcms_user_pay
(
   id                   int(11) unsigned not null auto_increment,
   user_id              int(11) unsigned not null,
   pay_type             tinyint(2) not null comment '0-水费，1-电费，2-气费，3-物管费',
   pay_to               int(11) unsigned not null,
   primary key (id),
   constraint FK_USER_PAY_TO_TO foreign key (pay_to)
      references xcms_pay_to (id) on update cascade
);

alter table xcms_user_pay comment '用户缴费';

/*==============================================================*/
/* Table: xcms_user_report                                      */
/*==============================================================*/
create table xcms_user_report
(
   id                   int(11) unsigned not null auto_increment,
   user_id              int(11) unsigned not null,
   reported_id          int(11) unsigned not null comment '被举报用户ID',
   cause                tinytext not null,
   primary key (id),
   constraint FK_UREPORT_REPORTER foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_user_report comment '用户举报表';

/*==============================================================*/
/* Table: xcms_user_role                                        */
/*==============================================================*/
create table xcms_user_role
(
   user_id              int(11) unsigned not null,
   role_id              int(11) unsigned not null,
   primary key (user_id, role_id),
   constraint FK_USER_ROLE_U foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade,
   constraint FK_USER_ROLE_R foreign key (role_id)
      references xcms_auth_roles (id) on delete cascade on update cascade
);

alter table xcms_user_role comment '用户角色关联表';

/*==============================================================*/
/* Table: xcms_user_trends                                      */
/*==============================================================*/
create table xcms_user_trends
(
   id                   int(11) unsigned not null auto_increment,
   user_id              int(11) unsigned not null,
   content              text not null,
   publish_time         int(11) unsigned not null,
   reply                int(5) unsigned not null default 0 comment '回复数量，默认为0',
   support              int(5) unsigned not null default 0 comment '赞数量，默认为0',
   primary key (id),
   constraint FK_USER_TRENDS_U foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_user_trends comment '用户动态发布';

/*==============================================================*/
/* Table: xcms_user_trends_pic                                  */
/*==============================================================*/
create table xcms_user_trends_pic
(
   id                   int(11) unsigned not null,
   msg_id               int(11) unsigned not null,
   url                  varchar(255) not null,
   primary key (id),
   constraint FK_USER_TRENDS_PIC_T foreign key (msg_id)
      references xcms_user_trends (id) on delete cascade on update cascade
);

alter table xcms_user_trends_pic comment '动态中的图片';

/*==============================================================*/
/* Table: xcms_user_trends_reply                                */
/*==============================================================*/
create table xcms_user_trends_reply
(
   id                   int(11) unsigned not null,
   trends_id            int(11) unsigned not null,
   user_id              int(11) unsigned not null,
   content              text not null,
   primary key (id),
   constraint FK_TRENDS_REPLY_T foreign key (trends_id)
      references xcms_user_trends (id) on delete cascade on update cascade,
   constraint FK_TRENDS_REPLY_U foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_user_trends_reply comment '动态回复表';

/*==============================================================*/
/* Table: xcms_user_trends_support                              */
/*==============================================================*/
create table xcms_user_trends_support
(
   trends_id            int(11) unsigned not null,
   user_id              int(11) unsigned not null,
   time                 int(11) unsigned not null,
   primary key (trends_id, user_id),
   constraint FK_TRENDS_SUPPORT_T foreign key (trends_id)
      references xcms_user_trends (id) on delete cascade on update cascade,
   constraint FK_TRENDS_SUPPORT_U foreign key (user_id)
      references xcms_user (id) on delete cascade on update cascade
);

alter table xcms_user_trends_support comment '赞动态';

