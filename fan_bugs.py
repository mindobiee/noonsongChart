from selenium import webdriver
from bs4 import BeautifulSoup
import pymysql
import time
import warnings
from selenium.common.exceptions import ElementNotInteractableException
from selenium.webdriver.common.keys import Keys
from webdriver_manager.chrome import ChromeDriverManager

print(time.strftime('%c', time.localtime(time.time())))
# ignore unique key duplicate warning
warnings.filterwarnings("ignore")

# make a cursor
conn = pymysql.connect(host="127.0.0.1", user='root', passwd='Song123~', db='gradproj', charset='utf8')
#conn = pymysql.connect(host="127.0.0.1", user='root', passwd='960919', db="gradualDB")
cur = conn.cursor()

# options to look like a human
options = webdriver.ChromeOptions()
options.add_argument("--no-sandbox")
options.add_argument("--headless")
options.add_argument("--disable-gpu")
options.add_argument("--disable-dev-shm-usage")
options.add_argument('--ignore-certificate-errors')
options.add_argument("--test-type")
options.add_argument("--disable-extesions") #added
options.add_argument("--remote-debugging-port=9222") #added
#options.add_argument("window-size=1920x1080")
options.add_argument("lang=ko_KR")
options.add_argument("user-agent=Chrome/77.0.3865.90")

#options.addArguments("test-type")
options.add_argument("--enable-precise-memory-info")
options.add_argument("--disable-popup-blocking")
options.add_argument("--disable-default-apps")
options.add_argument("test-type=browser")
options.add_argument("--incognito")

driver = webdriver.Chrome(ChromeDriverManager().install(), options=options)

driver.get("https://music.bugs.co.kr/chart")
source = driver.page_source
bs = BeautifulSoup(source, 'html.parser')

# ignore unique key duplicate warning
warnings.filterwarnings("ignore")

for i in range(0, 50):
    song_num = i + 1
    # page 전환
    xpath_songs = "/html/body/div[2]/div[2]/article/section/div/div[1]/table/tbody/tr[" + str(song_num) + "]/td[4]/a"
    button_songs = driver.find_element_by_xpath(xpath_songs)
    button_songs.send_keys(Keys.ENTER)
    driver.implicitly_wait(1)
    tmp = -1
    source = driver.page_source
    bs = BeautifulSoup(source, 'html.parser')

    print("crawling #", song_num)
    #title, albumTitle,artist,song_url
    title=driver.find_element_by_xpath('/html/body/div[2]/div[2]/article/header/div/h1').text.strip()
    artist=driver.find_element_by_xpath('/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[1]/td/a').text.strip()
    try:
        albumTitle=driver.find_element_by_xpath('/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[3]/td/a').text.strip()
    except :
        albumTitle=driver.find_element_by_xpath('/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[2]/td/a').text.strip()

    Id = title.replace('`', '\'').replace('‘', '\'').replace(',', '#').replace('&', '#').replace('(', '#').replace(
                    '[', '#').replace('<', '#').replace('{', '#').split('#')[0].replace(' ', '') + \
    albumTitle.replace('`', '\'').replace('‘', '\'').replace('<', '').replace('{', '').replace('(','').replace(
                  '[', '').split(' ')[0].replace(' ', '')


    # -----------------crawling part
    artist_likes=0
    artist_num = 1
    while 1 :
        #label: here
        try:
            #artist 가 한 명일 때
            # 수정 이전 코드는 example01.txt 에 있음.
            try : # if there are more than one artist,  the loop ends til, it draws the exception of calling over the last artist => except
                xpath_artist = "/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[1]/td/a[2]"
                feature = bs.select_one("#container > section.sectionPadding.summaryInfo.summaryTrack > div > div.basicInfo > table > tbody > tr:nth-child(1) > td > a:nth-child("+str(artist_num)+")").get_text().strip()
                print("try")
                if feature in title : #피처링한 가수가 artist 명단에 있으면, 제외시키고, 다음 가수로 넘어가기!
                    artist_num+=1
                xpath_artist = "/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[1]/td/a["+str(artist_num)+"]"
                artist_num +=1 #index jump to the next artist
                print("multi")
            except : #if there is an artist in the list, it breaks the loop til the end by the artist_num
                if artist_num >1 :
                    break
                print("single")
                xpath_artist = "/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[1]/td/a"
                artist_num= 0

            button_artist = driver.find_element_by_xpath(xpath_artist)
            driver.implicitly_wait(1)
            button_artist.send_keys(Keys.ENTER)
            source = driver.page_source
            bs = BeautifulSoup(source, 'html.parser')

            like = driver.find_element_by_xpath("/html/body/div[2]/div[2]/article/div/section[1]/div/div[2]/div[1]/a/span/em").text.strip().replace(",", "")
            artist_likes += int(like)
            print(str(artist_likes))

            #going back to the song_page
            driver.back()
            source = driver.page_source
            bs = BeautifulSoup(source, 'html.parser')

            if artist_num == 0 :
                break
                #goto crawling_end

        except:
            break

    #label: crawling_end
    query = """update musicList_bugs set artist_like =%s where id =%s;"""
    data = (str(artist_likes), Id)
    cur.execute(query, data)
    conn.commit()

    # going back to main chart page
    driver.get('https://music.bugs.co.kr/chart')
    source = driver.page_source

conn.commit()
cur.close()
conn.close()
driver.quit()
print(time.strftime('%c', time.localtime(time.time())))

