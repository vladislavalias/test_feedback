<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="/style.css" />
  </head>
  <body>
    <div class="container">
      <div class="page-content">
        <form id="feedback" method="post" action="feedback/save/" enctype="multipart/form-data">
          <div class="form-row error-row">
            <?php echo securityRenderErrors('feedback') ?>
          </div>
          <div class="form-row">
            <span>
              Заголовок письма:
            </span>
            <span>
              <input type="text" name="feedback[title]" />
            </span>
          </div>
          <div class="clearfix"></div>
          <div class="form-row">
            <span>
              Email:
            </span>
            <span>
              <input type="email" name="feedback[email]" />
            </span>
          </div>
          <div class="clearfix"></div>
          <div class="form-row">
            <span>
              Текст:
            </span>
            <span>
              <textarea name="feedback[text]"></textarea>
            </span>
          </div>
          <div class="clearfix"></div>
          <div class="form-row">
            <span>
              Выбирите файл:
            </span>
            <span>
              <input type="file" name="feedback[file]" />
            </span>
          </div>
          <div class="clearfix"></div>
          <div class="form-row">
            <input type="submit" name="form[submited]" value="Отправить" />
          </div>
        </form>
      </div>
    </div>
  </body>
</html>