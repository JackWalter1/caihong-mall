<?php
// 文章类子页共用骨架（head + header）—— 彩虹云商城 XHY-01
if (!isset($page_title)) $page_title = '平台内容';
if (!isset($page_desc)) $page_desc = '';
?>
<?php $page_title = '商品发布条例'; $page_desc = '用户不得在本平台发布禁售商品，禁售商品是指国家法律法规规定或本平台自身管理要求禁止发布及交易的商品或服务。'; ?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo htmlspecialchars($page_title); ?> - <?php echo $conf['sitename']; ?>-虚拟商品交易平台-虚拟卡密-知识付费-发卡网平台</title>
<meta name="description" content="<?php echo htmlspecialchars($page_desc ? $page_desc : $conf['description']); ?>">
<link rel="icon" href="/favicon.ico">
<link rel="stylesheet" href="/assets/plugin/hsmoban/css/style.css?v=202606161020">
</head>
<body class="subpage">
    <header class="site-header subpage-header">
        <div class="layout site-header__inner">
            <a class="brand" href="./" aria-label="<?php echo $conf['sitename']; ?>">
                <img src="template/XHY-01/assets/img/logo-full.png?v=20261023" width="164" height="44" alt="<?php echo $conf['sitename']; ?>logo">
            </a>
            <div class="header-actions">
                <a class="link-button" href="./">返回首页</a>
                <a class="service-action" href="./?mod=order">查询订单/申请售后</a>
            </div>
        </div>
    </header>
    <main class="subpage-main">

<section class="subpage-hero">
    <div class="layout">
        <span>平台内容</span>
        <h1>商品发布条例</h1>
        <p>2023-04-08</p>                </div>
</section>
<section class="layout subpage-card">
    <article class="article-body">
        <h4><strong>用户不得在本平台发布禁售商品，禁售商品是指国家法律法规规定或本平台自身管理要求禁止发布及交易的商品或服务。具体如下：</strong></h4><p>1.不得发布侵犯他人知识产权的商品，如假冒，盗版等。</p><p>2.不得发布带有欺诈信息的商品。</p><p>3.不得发布带有明显广告性质的商品。</p><p>4.不得发布带有任何诱导、赌博、色情信息的商品。</p><p>5.同一商品不得重复发布。</p><p>6.应如实描述出售的商品信息。</p><p>7.同时在卖的商品不得超过50个，部分品类会有特殊的限制。</p><p>8.同时在卖的票务品类不得超过3种票款，三个月内票务交易不得超过3单，且价格上浮不得超过10%。如为新开售演出票不得在开场72小时内售卖，平台有权对监控异常包括但不限于黄牛账号进行封禁。</p><p>9.根据国家法律有关规定，严禁发布以下类别的商品：</p><p>（1）反对宪法所确定的基本原则，危害国家安全、泄漏国家秘密、颠覆国家政权、破坏国家统一的；</p><p>（2）侵害他人名誉权、隐私权、商业秘密、商标权、著作权、专利权、其他知识产权及其他合法权益；</p><p>（3）违反依法律或本协议所应负的保密义务；</p><p>（4）冒用他人名义使用本平台服务；</p><p>（5）从事任何不法交易行为，如贩卖枪支、毒品、禁药、盗版软件、黄色淫秽物品或其他违禁物；</p><p>（6）提供赌博资讯或以任何方式引诱他人参与赌博、提供赌博平台充值入口；</p><p>（7）涉嫌洗钱、恐怖融资、套现或进行传销活动的；</p><p>（8）使用无效信用卡号码或他人信用卡号码进行交易；</p><p>（9）使用无效银行账户或他人银行账户交易；</p><p>（10）进行与您或交易对方宣称的内容不符的交易或不真实的交易；</p><p>（11）从事任何可能侵害本平台服务系統的行为；</p><p>（12）实际经营类型/商品与商家店铺认证时提交的经营类型/内容不符；</p><p>（13）冒用他人身份信息认证、授权在本平台销售产品的；</p><p>（14）其他违法行为或本平台有正当理由认为不适当的行为；</p><p>（15）连续90天没有交易发生的；</p><p>（16）销售国家禁止流通、限制流通商品或提供非法服务的；</p><p>（17）产品质量低劣给消费者造成重大损失或信誉低下的；</p><p>（18）累计3次以上经法院判决或仲裁对客户存在欺诈行为的；</p><p>（19）经公安机关、工商税务等国家部门认定已无业务资格的；</p><p>（20）危害国家安全，泄露国家秘密，颠覆国家政权，破坏国家统一的；</p><p>（21）损害国家荣誉和利益的；</p><p>（22）煽动民族仇恨、民族歧视，破坏民族团结的；</p><p>（23）破坏国家宗教政策，宣扬邪教和封建迷信的；</p><p>（24）散布谣言，扰乱社会秩序，破坏社会稳定的；</p><p>（25）散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪的；</p><p>（26）涉及赌博、洗钱、套现、传销等活动的；</p><p>（27）侮辱或者诽谤他人，侵害他人合法权益的；</p><p>（28）破坏计算机应用程序，如外挂、辅助等工具的；</p><p>（29）涉及知识产权侵权，如私服、无版权影视APP、盗版电影、音乐等的；</p><p>（30）含有欺骗性的，多次受到买家投诉的；</p><p>（31）您注册时确认的<strong>《平台禁售商品目录》</strong>规定的禁售商品的；</p><p>（32）商品的来源不合法或生产过程违法的；</p><p>（33）平台"<strong>风控公示</strong>"中公示的违规商品类型；</p><p>（34）其他违法行为或本平台有正当理由认为不适当的行为</p><p>10.法律法规禁止出售购买的其他情形。</p><p>11.您自本平台或经由本服务取得的任何建议或信息包括但不限于商品可售性、商品合法性、审核相关等信息，无论是书面或口头形式，除非本服务协议有明确规定，将不构成本服务协议以外之任何保证</p><p>特别注意：如您违反国家法律法规规定及以上规则发布和交易禁发禁售商品的，平台将无法保障您的权益，且有权删除商品，并对部分情节严重的用户进行相应处罚，包括但不限于限制账号功能、封禁账号。对于用户存在的涉嫌违法犯罪的行为，平台将移交司法机关进行处理。</p>
    </article>
</section>
<?php include TEMPLATE_ROOT.'XHY-01/_subpage_foot.inc.php'; ?>
