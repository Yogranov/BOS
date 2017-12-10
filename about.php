<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 18-Oct-17
 * Time: 19:58
 */


namespace BugOrderSystem;

require_once "Classes/BugOrderSystem.php";



$html = <<<EOF
<!DOCTYPE html>
<html>
<head>
    <title>אודות</title>
    
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">

    <!-- jquery Core -->
        <script
        src="https://code.jquery.com/jquery-3.2.1.min.js" 
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" 
        crossorigin="anonymous"></script>
        
     <!-- jquery ui -->
        <script
        src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" 
        integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" 
        crossorigin="anonymous"></script>
  
          <!-- Bootstrap 3 -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

  
    <!--site main js-->
    <script src="js/main.js"></script>
    <script src="js/jqueryMain.js"></script>
    
    <meta name="viewport" content="width=device-width">
</head>
<body>

        <nav class="navbar navbar-default">
      <div class="container-fluid">
        <logo>
            <img src="images/logo.png" alt="logo">
        </logo>
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-2">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse-2">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="about.php">אודות <i class="glyphicon glyphicon-comment"></i></a></li>
            <li><a href="login.php">כניסה <i class="glyphicon glyphicon-log-in"></i></a></li>
          </ul>
        </div>
      </div>
    </nav>

<!--
    <header>
       <div class="wrapper">
          <logo>
              <img src="images/logo.png" alt="logo">
          </logo>
          <nav>
           <div id="burger-nav"><img src="images/icons/burger.png" alt="exit"></div>
           <h2>Main Navigation</h2>
              <ul>
                  <li><a href="about.php" class="current">אודות</a></li>
                  <li><a href="index.php">כניסה</a></li>
              </ul>
          </nav>
      </div>
    </header>
    -->       
    <main>
            <div class="wrapper">     
                <div id="container" style="direction: rtl;">
                  <div class="title"><h1>שאלות ותשובות</h1></div>
                  <ul class="faq">
                    <li class="q"><img src="images/icons/arrow.png"> מה זו המערכת?</li>
                    <li class="a">                המערכת הנה מערכת רשתית העוזרת לביצוע ניהול נכון של הסניף. 
                            המערכת מרכזת הזמנת מוצרים עבור לקוחות המחפשים מוצרים מסוימים שלא קיימים בסניף.
                             ההזמנות נשמרות בצורה מסודרת ונוחה לניהול.
                             בנוסף המערכת מסייעת בתזכור הסניף והמוכרים על תהליך ההזמנה. בנוסף למערכת גם לוח תזכורות סניפי.</li>
                    
                   
                    <li class="q"><img src="images/icons/arrow.png"> מאיפה זה התחיל?</li>
                    <li class="a">                המערכת שהנך רואה היא סוג של אבולוציה שנוצרה מהצורך בשיטה נוחה ויעילה בניהול נכון של מלאי מוצרים אשר אינו קיים בסניף ויכול לעזור בהגדלת המכירות.
                             אין מוצר שאין לנו, תמיד אפשר להשיג מוצר- מהזול ועד היקר.
                             ברגע שיש אופציה טובה לניהול המלאי הזה - המלאי זורם בקצב הנכון ומגיע ללקוחות בצורה המהירה ביותר וללא פספוסים.
                              בעבר היינו כותבים על דף ונועצים, שיטה מאוד מסורבלת הסובלת מחוסר עדכון ובעיות הבנת הכתוב.
                             לאחר מכן עברנו לטבלה מודפסת ומסודרת עם תאריכים, מוכרים ואפילו מקום להערות.
                             גם שיטה זו סבלה מבעית הבנה של כתב היד וחוסר עדכון בין העובדים.
                             לאחר מכן עברנו לדף גוגל עם כלל הפרטים אשר נשלחים לקובץ שיטס (אקסל של גוגל) בצורה מסודרת.
                             את הדף סידרנו בצורה נוחה עם סטאטוסי הזמנה וצבעים מתאימים. השיטה האחרונה עבדה נהדר במשך הרבה זמן.
                             עם הזמן צברנו המון צרכים חדשים שהמערכת לא ידעה לתת להם תשובה, ולכן נוצרה המערכת שלנו.</li>
                    
                    <li class="q"><img src="images/icons/arrow.png"> איך זה עובד?</li>
                    <li class="a">המערכת בנויה בצורה חכמה מאוד עם המון מחשבה וארגון מקדים, שני הדברים האלו עוזרים למערכת לעבוד בצורה חלקה ומהירה מאוד וכמעט ללא תקלות.
            המערכת בנויה מכמה חלקים:
                            <br>
                                <ul>
                                <li>
                                 1. פתיחת ההזמנה:
             דף איסוף נתונים הכולל את פרטי הלקוח, פרטי ההזמנה והמוצרים שירצה להזמין. דף זה מסונכרן עם מאגר המידע, ברגע שלקוח יצר הזמנה ברשת הוא נכנס ובפעם הבאה המערכת תשלוף את נתוניו בצורה אוטומטית על פי מספר הפלאפון האישי.
                                </li>
                                <li>
                                  2. שמירת הנתונים בצורה נוחה תחת מספר טבלות:  
                                
                                  <br>&nbsp;
             א. הזמנות פתוחות - מהרגע שההזמנה נפתחה ועד שתיסגר, ההזמנה תעבור מספר סטאטוסים שונים (כגון - הוזמן, בדרך, הגיע, לקוח מעודכן וכו'..).
                                  
                                 <br>&nbsp;
                    ב. הזמנות סגורות - הזמנות שבוטלו או נאספו יעברו בצורה אוטומטית לטבלה אחרת שם יאוחסנו לשימושים עתידיים.
                                  
                                  <br>&nbsp;
            ג. הזמנות מוקדמות - לצורך שמירה על הסדר ובשביל שלא יהיו בטבלת ההזמנות משקעים. הטבלה תופיע רק במידה ויש הזמנות בסטאטוס "הזמנה מוקדמת"..
                                   
                                    </li>
                                    <li>
                                3.  ניהול תזכורות - 
            בדף הראשי של המערכת תופיע תמיד טבלת תזכורות מידיות, במידה וצריך להשאיר הודעה ולא רוצים שהיא תאבד או תמחק, המערכת מאפשרת השארת הודעות בדף הראשי בצורה מהירה ויעילה.
                               </li>
                               <li>
                                4. מערכת ניהול הודעות: 
                               <br>&nbsp;
            א. בשלב יצירת ההזמנה הנך יכול לקחת מהלקוח כתובת דואר אלקטרוני במידה וירצה. ברגע שההזמנה תגיע לסניף המערכת תקפיץ לך הודעה ותשאל האם לשלוח ללקוח הודעה שההזמנה הגיעה. במידה ותלחץ "שלח אימייל" המערכת תשלח אימייל ללקוח ותעדכן אותו כי ההזמנה שלו מוכנה באותו הסניף.
            במידה ולא תרצה פשוט ההזמנה תתעדכן ל-'הגיעה לסניף'.
                               
                               <br>&nbsp;
            ב. במידה ומוכר פתח הזמנה ושכח ממנה (נשארה בסטאטוס "הזמנה פתוחה"), למחרת בבוקר המערכת תתריע באימייל למוכר ובמקביל לסניף על אותה הזמנה שאינה מעודכנת וצריך להזמינה. ההודעה תשלח בבוקר לפני פתיחת הסניף, דבר שיכול להציל את דחיית ההזמנה בעוד יום נוסף.
                                   
                                   
                               </ul>
                            </li>

                    <li class="q"><img src="images/icons/arrow.png"> מערכת ניהול ובקרה</li>
                    <li class="a">למערכת ישנה מערכת משנה המתנהלת על ידי מנהלי איזור, לכל מנהל איזור יש סניפים עם התראות במקרים חריגים וניתוח סטאטיסטי.</li>
                    
                    <li class="q"><img src="images/icons/arrow.png"> מערכת שליחת מיילים</li>
                    <li class="a">בפתיחת ההזמנה הלקוח מקבל הודעה למייל עם פרטי הזמנה ומעקב אחר סטאטוס הזמנה.
                     כשהמוצר מגיע לסניף הלקוח מקבל הודעה שהמוצר הגיע, ברגע שהלקוח יפתח את המייל המערכת תתעדכן אוטומטי שהלקוח קרא ומעודכן שההזמנה מוכנה לו בסניף.</li>
                    
                    <li class="a">המערכת מאופיינת בצורה טובה, נוחה ומהירה. בנוסף עוזרת לשמור על קצב ההזמנות סדיר ללא המתנות.</li>
             
                              <li class="q"><img src="images/icons/arrow.png"> מה צופן העתיד?</li>
                    <li class="a">                    יש עוד הרבה תכניות לעתיד, כגון - <ul>
                                          <li>
            א. שליחת הזמנה ישירות לסניף או איש הרכש בצורה אוטומטית בעת פתיחת ההזמנה במערכת (צריך לוודא שזה תקין לשלוח מכתובת חיצונית).
                                          </li>
                                         <li>
            ב.  שדרוג מערכת ההתראות אל הסניף- הודעות שונות במצבים שונים יעזרו לקדם הזמנות שלא ישארו בהמתנה ועוד מחשבות לעתיד.
                                        </li>
                                        <li>
            ג. היד עוד נטויה :)
                                        </li>
                                    </ul>
                        </li>       
                     </ul>
                </div>
            </div>
        </main>

    </body>

<footer>

</footer>


</html>
EOF;

echo $html;
