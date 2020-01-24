from selenium import webdriver
from webdriver_manager.chrome import ChromeDriverManager
from bs4 import BeautifulSoup
from urllib.request import urlopen
import pymysql 
import astropy.io.misc.asdf.tags.time.tests.test_time

driver = webdriver.Chrome(ChromeDriverManager().install())

# create cursor
conn = pymysql.connect(host = "127.0.0.1",
                       user = 'root', passwd = '132365', db = 'gradproj')
cur = conn.cursor()

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
# alert 만나면 확인버튼 클릭 후 False 리턴
def change_pages():
    # '다음 페이지' 버튼 찾기
    temp = driver.find_element_by_class_name("page-nav")
    button_pages = temp.find_element_by_class_name("next")  

    # 버튼 클릭
    button_pages.click()
    
    # 마지막 페이지일 경우 뜨는 alert 제어 
    try:
        alert = Alert(driver)
        alert.accept()

    # 마지막 페이지 아닌 경우 
    except NoAlertPresentException:
        return True

    return False

# 메인화면에서 곡 선택 하는 함수
def change_songs(song_num):
    source = driver.page_source
    bs = BeautifulSoup(source, 'html.parser')
    
    # 첫 번째 곡부터 순서대로 버튼 찾아 선택
    xpath_songs = "html/body/div[3]/div[2]/div[1]/div[6]/div/table/tbody/tr[" + str(song_num) + "]/td[4]/a"
    button_songs = driver.find_element_by_xpath(xpath_songs)
    button_songs.click()

    # 아이피 차단 방지용
    time.sleep(1)

    # 댓글 1페이지 크롤링
    crawlComments(bs)

    while(change_pages()):
        source = driver.page_source
        bs = BeautifulSoup(source, 'html.parser')

        crawlComments(bs)
        
    # 한 곡의 댓글 모두 크롤링한 후 메인페이지로 돌아감
    driver.get('https://genie.co.kr/chart/top200')
    source = driver.page_source

# 댓글 크롤링하는 함수
def crawlComments(bs):
    replies = bs.find_all(class_="reply-text")

    writerId = [ i.find("strong").find("a").get_text() for i in replies ]
    comment = [ i.find("p").get_text() for i in replies]

    # 각 곡별 페이지별 댓글들 DB에 저장
    for i in range(0, len(writerId)):
        query = """
            insert ignore into comments (writerId, comment) values ('%s', '%s");
        """%( str(writerId[i]), str(comment[i]) )
            
        cur.execute(query)

    time.sleep(1)
    # conn.commit()

# 1위부터 100위까지 100곡 크롤링할 것 
for i in range(1, 2):
    # 첫 페이지에 50위, 51위부터는 버튼 한 번 클릭해야 보임
    if i == 50:
        fiftyone_to_hundred = driver.find_element_by_link_text("51 ~ 100 위")
        fiftyone_to_hundred.click()

        change_songs(i) 
    else:
        change_songs(i) 

# 닫기
conn.commit()
cur.close()
conn.close()
driver.quit() 