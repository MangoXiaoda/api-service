# 美香后台

#### 介绍
商城服务后台

#### 软件架构
软件架构说明

#### 安装教程

1.  git clone git@github.com:MangoXiaoda/api-service.git
2.  cp .env.example .env
3.  composer install / composer update nothing
4.  php artisan key:generate
5.  php artisan admin:publish
6.  php artisan admin:install
7.  php artisan jwt:secret

#### 使用说明

1.  xxxx
2.  xxxx
3.  xxxx

#### 参与贡献

1.  Fork 本仓库
2.  新建 Feat_xxx 分支
3.  提交代码
4.  新建 Pull Request


#### Commit提交规范
> 格式: type(scope) : subject
##### type: 本次 commit 的类型，诸如 bugfix、docs、style 等，参考如下
- feat：添加新功能
- fix：修补缺陷
- docs：修改文档
- style：修改格式
- refactor：重构
- perf：优化
- test：增加测试
- chore：构建过程或辅助工具的变动
- revert：回滚到上一个版本

##### scope: 本次 commit 波及的范围(一般为文件名)

##### subject: 简明扼要的阐述下本次 commit 的主旨(结尾无需添加标点)


