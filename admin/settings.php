<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';
require_admin();

$settings = get_settings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf()) die('CSRF error');

  $stmt = db()->prepare('UPDATE settings SET instagram_url=:ig, whatsapp_number=:wa,
    site_title_az=:staz, site_title_ru=:stru, site_title_en=:sten,
    topbar_text_az=:taz, topbar_text_ru=:tru, topbar_text_en=:ten,
    delivery_text_az=:daz, delivery_text_ru=:dru, delivery_text_en=:den,
    hero_tagline_az=:htaz, hero_tagline_ru=:htru, hero_tagline_en=:hten,
    hero_title_az=:h1az, hero_title_ru=:h1ru, hero_title_en=:h1en,
    hero_subtitle_az=:hsaz, hero_subtitle_ru=:hsru, hero_subtitle_en=:hsen,
    hero_location_az=:hlaz, hero_location_ru=:hlru, hero_location_en=:hlen,
    home_gift_title_az=:g1az, home_gift_title_ru=:g1ru, home_gift_title_en=:g1en,
    home_gift_text_az=:g2az, home_gift_text_ru=:g2ru, home_gift_text_en=:g2en,
    about_title_az=:a1az, about_title_ru=:a1ru, about_title_en=:a1en,
    about_subtitle_az=:a2az, about_subtitle_ru=:a2ru, about_subtitle_en=:a2en,
    about_text_1_az=:a3az, about_text_1_ru=:a3ru, about_text_1_en=:a3en,
    about_text_2_az=:a4az, about_text_2_ru=:a4ru, about_text_2_en=:a4en,
    give_steps_title_az=:s1az, give_steps_title_ru=:s1ru, give_steps_title_en=:s1en,
    give_step_1_az=:s2az, give_step_1_ru=:s2ru, give_step_1_en=:s2en,
    give_step_2_az=:s3az, give_step_2_ru=:s3ru, give_step_2_en=:s3en,
    give_step_3_az=:s4az, give_step_3_ru=:s4ru, give_step_3_en=:s4en,
    giveaway_winner_title_az=:w1az, giveaway_winner_title_ru=:w1ru, giveaway_winner_title_en=:w1en,
    default_meta_description_az=:mAz, default_meta_description_ru=:mRu, default_meta_description_en=:mEn,
    og_image_path=:og, updated_at=NOW() WHERE id=1');
  $stmt->execute([
    ':ig' => $_POST['instagram_url'],
    ':wa' => $_POST['whatsapp_number'],
    ':staz' => $_POST['site_title_az'],
    ':stru' => $_POST['site_title_ru'],
    ':sten' => $_POST['site_title_en'],
    ':taz' => $_POST['topbar_text_az'],
    ':tru' => $_POST['topbar_text_ru'],
    ':ten' => $_POST['topbar_text_en'],
    ':daz' => $_POST['delivery_text_az'],
    ':dru' => $_POST['delivery_text_ru'],
    ':den' => $_POST['delivery_text_en'],
    ':htaz' => $_POST['hero_tagline_az'],
    ':htru' => $_POST['hero_tagline_ru'],
    ':hten' => $_POST['hero_tagline_en'],
    ':h1az' => $_POST['hero_title_az'],
    ':h1ru' => $_POST['hero_title_ru'],
    ':h1en' => $_POST['hero_title_en'],
    ':hsaz' => $_POST['hero_subtitle_az'],
    ':hsru' => $_POST['hero_subtitle_ru'],
    ':hsen' => $_POST['hero_subtitle_en'],
    ':hlaz' => $_POST['hero_location_az'],
    ':hlru' => $_POST['hero_location_ru'],
    ':hlen' => $_POST['hero_location_en'],
    ':g1az' => $_POST['home_gift_title_az'],
    ':g1ru' => $_POST['home_gift_title_ru'],
    ':g1en' => $_POST['home_gift_title_en'],
    ':g2az' => $_POST['home_gift_text_az'],
    ':g2ru' => $_POST['home_gift_text_ru'],
    ':g2en' => $_POST['home_gift_text_en'],
    ':a1az' => $_POST['about_title_az'],
    ':a1ru' => $_POST['about_title_ru'],
    ':a1en' => $_POST['about_title_en'],
    ':a2az' => $_POST['about_subtitle_az'],
    ':a2ru' => $_POST['about_subtitle_ru'],
    ':a2en' => $_POST['about_subtitle_en'],
    ':a3az' => $_POST['about_text_1_az'],
    ':a3ru' => $_POST['about_text_1_ru'],
    ':a3en' => $_POST['about_text_1_en'],
    ':a4az' => $_POST['about_text_2_az'],
    ':a4ru' => $_POST['about_text_2_ru'],
    ':a4en' => $_POST['about_text_2_en'],
    ':s1az' => $_POST['give_steps_title_az'],
    ':s1ru' => $_POST['give_steps_title_ru'],
    ':s1en' => $_POST['give_steps_title_en'],
    ':s2az' => $_POST['give_step_1_az'],
    ':s2ru' => $_POST['give_step_1_ru'],
    ':s2en' => $_POST['give_step_1_en'],
    ':s3az' => $_POST['give_step_2_az'],
    ':s3ru' => $_POST['give_step_2_ru'],
    ':s3en' => $_POST['give_step_2_en'],
    ':s4az' => $_POST['give_step_3_az'],
    ':s4ru' => $_POST['give_step_3_ru'],
    ':s4en' => $_POST['give_step_3_en'],
    ':w1az' => $_POST['giveaway_winner_title_az'],
    ':w1ru' => $_POST['giveaway_winner_title_ru'],
    ':w1en' => $_POST['giveaway_winner_title_en'],
    ':mAz' => $_POST['default_meta_description_az'],
    ':mRu' => $_POST['default_meta_description_ru'],
    ':mEn' => $_POST['default_meta_description_en'],
    ':og' => $_POST['og_image_path']
  ]);
  header('Location: settings.php');
  exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Ayarlar</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
<section class="section">
  <div class="container">
    <h1>Ayarlar</h1>
    <div class="card" style="margin-bottom:16px;">
      <strong>Not</strong><br />
      Kampaniya ve hediyye cekilisi ayarlari ayrica bolumlerden idare olunur.
    </div>
    <form class="order-form" method="post">
      <?php echo csrf_field(); ?>
      <label>Instagram URL <input type="text" name="instagram_url" value="<?php echo e($settings['instagram_url'] ?? ''); ?>" /></label>
      <label>WhatsApp nomresi <input type="text" name="whatsapp_number" value="<?php echo e($settings['whatsapp_number'] ?? ''); ?>" /></label>
      <label>OG shekil yolu <input type="text" name="og_image_path" value="<?php echo e($settings['og_image_path'] ?? ''); ?>" /></label>

      <label>Sayt basliq AZ <input type="text" name="site_title_az" value="<?php echo e($settings['site_title_az'] ?? ''); ?>" /></label>
      <label>Sayt basliq RU <input type="text" name="site_title_ru" value="<?php echo e($settings['site_title_ru'] ?? ''); ?>" /></label>
      <label>Sayt basliq EN <input type="text" name="site_title_en" value="<?php echo e($settings['site_title_en'] ?? ''); ?>" /></label>

      <label>Topbar AZ <input type="text" name="topbar_text_az" value="<?php echo e($settings['topbar_text_az'] ?? ''); ?>" /></label>
      <label>Topbar RU <input type="text" name="topbar_text_ru" value="<?php echo e($settings['topbar_text_ru'] ?? ''); ?>" /></label>
      <label>Topbar EN <input type="text" name="topbar_text_en" value="<?php echo e($settings['topbar_text_en'] ?? ''); ?>" /></label>

      <label>Catdirilma AZ <input type="text" name="delivery_text_az" value="<?php echo e($settings['delivery_text_az'] ?? ''); ?>" /></label>
      <label>Catdirilma RU <input type="text" name="delivery_text_ru" value="<?php echo e($settings['delivery_text_ru'] ?? ''); ?>" /></label>
      <label>Catdirilma EN <input type="text" name="delivery_text_en" value="<?php echo e($settings['delivery_text_en'] ?? ''); ?>" /></label>

      <label>Hero Tagline AZ <input type="text" name="hero_tagline_az" value="<?php echo e($settings['hero_tagline_az'] ?? ''); ?>" /></label>
      <label>Hero Tagline RU <input type="text" name="hero_tagline_ru" value="<?php echo e($settings['hero_tagline_ru'] ?? ''); ?>" /></label>
      <label>Hero Tagline EN <input type="text" name="hero_tagline_en" value="<?php echo e($settings['hero_tagline_en'] ?? ''); ?>" /></label>

      <label>Hero Basliq AZ <input type="text" name="hero_title_az" value="<?php echo e($settings['hero_title_az'] ?? ''); ?>" /></label>
      <label>Hero Basliq RU <input type="text" name="hero_title_ru" value="<?php echo e($settings['hero_title_ru'] ?? ''); ?>" /></label>
      <label>Hero Basliq EN <input type="text" name="hero_title_en" value="<?php echo e($settings['hero_title_en'] ?? ''); ?>" /></label>

      <label>Hero Subtitle AZ <textarea name="hero_subtitle_az"><?php echo e($settings['hero_subtitle_az'] ?? ''); ?></textarea></label>
      <label>Hero Subtitle RU <textarea name="hero_subtitle_ru"><?php echo e($settings['hero_subtitle_ru'] ?? ''); ?></textarea></label>
      <label>Hero Subtitle EN <textarea name="hero_subtitle_en"><?php echo e($settings['hero_subtitle_en'] ?? ''); ?></textarea></label>

      <label>Hero Lokasiya AZ <input type="text" name="hero_location_az" value="<?php echo e($settings['hero_location_az'] ?? ''); ?>" /></label>
      <label>Hero Lokasiya RU <input type="text" name="hero_location_ru" value="<?php echo e($settings['hero_location_ru'] ?? ''); ?>" /></label>
      <label>Hero Lokasiya EN <input type="text" name="hero_location_en" value="<?php echo e($settings['hero_location_en'] ?? ''); ?>" /></label>

      <label>Gift Basliq AZ <input type="text" name="home_gift_title_az" value="<?php echo e($settings['home_gift_title_az'] ?? ''); ?>" /></label>
      <label>Gift Basliq RU <input type="text" name="home_gift_title_ru" value="<?php echo e($settings['home_gift_title_ru'] ?? ''); ?>" /></label>
      <label>Gift Basliq EN <input type="text" name="home_gift_title_en" value="<?php echo e($settings['home_gift_title_en'] ?? ''); ?>" /></label>

      <label>Gift Metn AZ <textarea name="home_gift_text_az"><?php echo e($settings['home_gift_text_az'] ?? ''); ?></textarea></label>
      <label>Gift Metn RU <textarea name="home_gift_text_ru"><?php echo e($settings['home_gift_text_ru'] ?? ''); ?></textarea></label>
      <label>Gift Metn EN <textarea name="home_gift_text_en"><?php echo e($settings['home_gift_text_en'] ?? ''); ?></textarea></label>

      <label>Haqqimizda Basliq AZ <input type="text" name="about_title_az" value="<?php echo e($settings['about_title_az'] ?? ''); ?>" /></label>
      <label>Haqqimizda Basliq RU <input type="text" name="about_title_ru" value="<?php echo e($settings['about_title_ru'] ?? ''); ?>" /></label>
      <label>Haqqimizda Basliq EN <input type="text" name="about_title_en" value="<?php echo e($settings['about_title_en'] ?? ''); ?>" /></label>

      <label>Haqqimizda Subtitle AZ <input type="text" name="about_subtitle_az" value="<?php echo e($settings['about_subtitle_az'] ?? ''); ?>" /></label>
      <label>Haqqimizda Subtitle RU <input type="text" name="about_subtitle_ru" value="<?php echo e($settings['about_subtitle_ru'] ?? ''); ?>" /></label>
      <label>Haqqimizda Subtitle EN <input type="text" name="about_subtitle_en" value="<?php echo e($settings['about_subtitle_en'] ?? ''); ?>" /></label>

      <label>Haqqimizda Metn 1 AZ <textarea name="about_text_1_az"><?php echo e($settings['about_text_1_az'] ?? ''); ?></textarea></label>
      <label>Haqqimizda Metn 1 RU <textarea name="about_text_1_ru"><?php echo e($settings['about_text_1_ru'] ?? ''); ?></textarea></label>
      <label>Haqqimizda Metn 1 EN <textarea name="about_text_1_en"><?php echo e($settings['about_text_1_en'] ?? ''); ?></textarea></label>

      <label>Haqqimizda Metn 2 AZ <textarea name="about_text_2_az"><?php echo e($settings['about_text_2_az'] ?? ''); ?></textarea></label>
      <label>Haqqimizda Metn 2 RU <textarea name="about_text_2_ru"><?php echo e($settings['about_text_2_ru'] ?? ''); ?></textarea></label>
      <label>Haqqimizda Metn 2 EN <textarea name="about_text_2_en"><?php echo e($settings['about_text_2_en'] ?? ''); ?></textarea></label>

      <label>Giveaway Steps Basliq AZ <input type="text" name="give_steps_title_az" value="<?php echo e($settings['give_steps_title_az'] ?? ''); ?>" /></label>
      <label>Giveaway Steps Basliq RU <input type="text" name="give_steps_title_ru" value="<?php echo e($settings['give_steps_title_ru'] ?? ''); ?>" /></label>
      <label>Giveaway Steps Basliq EN <input type="text" name="give_steps_title_en" value="<?php echo e($settings['give_steps_title_en'] ?? ''); ?>" /></label>

      <label>Addim 1 AZ <input type="text" name="give_step_1_az" value="<?php echo e($settings['give_step_1_az'] ?? ''); ?>" /></label>
      <label>Addim 1 RU <input type="text" name="give_step_1_ru" value="<?php echo e($settings['give_step_1_ru'] ?? ''); ?>" /></label>
      <label>Addim 1 EN <input type="text" name="give_step_1_en" value="<?php echo e($settings['give_step_1_en'] ?? ''); ?>" /></label>

      <label>Addim 2 AZ <input type="text" name="give_step_2_az" value="<?php echo e($settings['give_step_2_az'] ?? ''); ?>" /></label>
      <label>Addim 2 RU <input type="text" name="give_step_2_ru" value="<?php echo e($settings['give_step_2_ru'] ?? ''); ?>" /></label>
      <label>Addim 2 EN <input type="text" name="give_step_2_en" value="<?php echo e($settings['give_step_2_en'] ?? ''); ?>" /></label>

      <label>Addim 3 AZ <input type="text" name="give_step_3_az" value="<?php echo e($settings['give_step_3_az'] ?? ''); ?>" /></label>
      <label>Addim 3 RU <input type="text" name="give_step_3_ru" value="<?php echo e($settings['give_step_3_ru'] ?? ''); ?>" /></label>
      <label>Addim 3 EN <input type="text" name="give_step_3_en" value="<?php echo e($settings['give_step_3_en'] ?? ''); ?>" /></label>

      <label>Qalibler Basliq AZ <input type="text" name="giveaway_winner_title_az" value="<?php echo e($settings['giveaway_winner_title_az'] ?? ''); ?>" /></label>
      <label>Qalibler Basliq RU <input type="text" name="giveaway_winner_title_ru" value="<?php echo e($settings['giveaway_winner_title_ru'] ?? ''); ?>" /></label>
      <label>Qalibler Basliq EN <input type="text" name="giveaway_winner_title_en" value="<?php echo e($settings['giveaway_winner_title_en'] ?? ''); ?>" /></label>

      <label>Meta AZ <textarea name="default_meta_description_az"><?php echo e($settings['default_meta_description_az'] ?? ''); ?></textarea></label>
      <label>Meta RU <textarea name="default_meta_description_ru"><?php echo e($settings['default_meta_description_ru'] ?? ''); ?></textarea></label>
      <label>Meta EN <textarea name="default_meta_description_en"><?php echo e($settings['default_meta_description_en'] ?? ''); ?></textarea></label>

      <button class="btn primary" type="submit">Yadda saxla</button>
      <a class="btn ghost" href="index.php">Geri</a>
    </form>
  </div>
</section>
</body>
</html>
