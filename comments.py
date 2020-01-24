from selenium import webdriver
from webdriver_manager.chrome import ChromeDriverManager
from bs4 import BeautifulSoup
from urllib.request import urlopen
import pymysql 
import time
from selenium.webdriver.common.alert import Alert
from selenium.common.exceptions import NoAlertPresentException

driver = webdriver.Chrome(ChromeDriverManager().install())

# create cursor

conn = pymysql.connect(host = "127.0.0.1",
                       user = 'root', passwd = '132365', db = 'gradproj')

# options to look like a human
options = webdriver.ChromeOptions()
options.add_argument("--headless")
options.add_argument("--no-sandbox")
options.add_argument("--disable-gpu")

options.add_argument("window-size=1920x1080")
options.add_argument("lang=ko_KR")
options.add_argument("user-agent=Chrome/77.0.3865.90")

driver.get('https://genie.co.kr/chart/top200')
source = driver.page_source

bs = BeautifulSoup(source, 'html.parser')

# 각 곡 상세 페이지에서 댓글 페이지 바꾸는 함수
# 페이지 옆에 다음 버튼 계속 누름. 마지막 페이지라는 alert 뜰 때까지
def change_pages():
    temp = driver.find_element_by_class_name("page-nav")
    button_pages = temp.find_element_by_class_name("next")  

    button_pages.click()
    
    try:
        alert = Alert(driver)
        alert.accept()
        time.sleep(1)

    except NoAlertPresentException:
        return True

    return False

# 메인화면에서 곡 선택 하는 함수
def change_songs(song_num):
    source = driver.page_source
    bs = BeautifulSoup(source, 'html.parser')
    
    xpath_songs = "html/body/div[3]/div[2]/div[1]/div[6]/div/table/tbody/tr[" + str(song_num) + "]/td[4]/a"
    button_songs = driver.find_element_by_xpath(xpath_songs)
    button_songs.click()

    time.sleep(1)

    # crawl page 1
    crawlComments(bs)

    while(change_pages()):
        source = driver.page_source
        bs = BeautifulSoup(source, 'html.parser')

        crawlComments(bs)
        
    driver.get('https://genie.co.kr/chart/top200')
    source = driver.page_source

def crawlComments(bs):
    cur = conn.cursor()

    replies = bs.find_all(class_="reply-text")

    writerId = [ i.find("strong").find("a").get_text() for i in replies ]
    comment = [ i.find("p").get_text() for i in replies]

        # 각 곡별 페이지별 댓글들 DB에 저장
    for i in range(0, len(writerId)):
        query = """
            insert into comments_ (writerId, comment) values ('%s', '%s')
        """%( str(writerId[i]), str(comment[i]) )
            
        try:
            cur.execute(query)
        except pymysql.IntegrityError:
            conn.commit()
            cur.close()
            cur = conn.cursor()

            continue

for i in range(6, 7):
    change_songs(i)

conn.commit()
cur.close()
conn.close()
driver.quit() 