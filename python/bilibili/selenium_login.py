"""
selenium模拟登陆B站
"""
import time
import collections
from io import BytesIO
from configparser import ConfigParser

import cv2
from PIL import Image
from selenium import webdriver
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.wait import WebDriverWait

JS = """
document.querySelector('canvas[class="geetest_canvas_fullbg geetest_fade geetest_absolute"]').style = ""
"""


class BilibiliLogin:
    """登陆验证类"""

    def __init__(self):
        config = ConfigParser()
        config.read('.config', encoding='UTF-8')
        user = config['user']
        self.username = user['username']
        self.password = user['password']
        self.browser = None
        self.wait = None

    def send_key(self):
        """
        设置账户密码并点击登录刷出滑动验证
        """
        uname = self.browser.find_element_by_id("login-username")
        uname.send_keys(self.username)

        pwd = self.browser.find_element_by_id("login-passwd")
        pwd.send_keys(self.password)

        login = self.browser.find_element_by_xpath(
            '//a[@class="btn btn-login"]')
        login.click()

    def shot_screen(self):
        """
        截屏保存文件
        """
        screenshot = self.browser.get_screenshot_as_png()
        screenshot = Image.open(BytesIO(screenshot))
        return screenshot

    def shot_origin_screen(self):
        """
        执行JS获取原始图片
        """
        self.browser.execute_script(JS)

    def get_position(self):
        """
        获取图片位置
        """
        img = WebDriverWait(self.browser, 20).until(
            EC.presence_of_element_located(
                (By.XPATH,
                 '//div[@class="geetest_canvas_img geetest_absolute"]')
            )
        )
        location = img.location
        size = img.size
        top, bottom, left, right = (
            location["y"],
            location["y"] + size["height"],
            location["x"],
            location["x"] + size["width"],
        )
        return top, bottom, left, right

    def get_geetest_image(self, position, name="img.png"):
        """
        获取验证码图片
        :param name:
        :return:  图片验证码
        """
        top, bottom, left, right = position
        screenshot = self.shot_screen()
        img = screenshot.crop((left, top, right, bottom))
        img.save(name)
        return img

    def mouse_action(self):
        """
        拖动元素
        """
        dragger = self.browser.find_element_by_xpath(
            '//div[@class="geetest_slider_button"]'
        )
        ActionChains(self.browser).click_and_hold(dragger).perform()
        gap = self.get_gap()
        ActionChains(self.browser).move_by_offset(
            xoffset=gap, yoffset=0).perform()
        time.sleep(0.5)
        ActionChains(self.browser).release().perform()

    def get_gap(self, img1="img.png", img2="img1.png"):
        """
        通过比较两块地方的色差，获取滑动距离
        """
        img = cv2.imread(img1, 0)
        img1 = cv2.imread(img2, 0)
        dif1 = []
        dif2 = []
        for offset1 in range(img.shape[0]):
            for offset2 in range(img.shape[1]):
                if abs(int(img[offset1][offset2]) - int(img1[offset1][offset2])) > 40:
                    if offset2 < 50 and offset2 > 3:
                        dif1.append(offset2)
                    elif offset2 > 50:
                        dif2.append(offset2)

        obj1 = collections.Counter(dif1)
        obj2 = collections.Counter(dif2)
        list1 = []
        list2 = []
        n = 25
        for line in obj1.most_common():
            if line[1] > n:
                list1.append(line[0])
        for line in obj2.most_common():
            if line[1] > n:
                list2.append(line[0])
        gap = max(list2) - max(list1)
        return gap

    def login(self):
        """
        启动入口
        """
        self.browser = webdriver.Chrome()
        self.browser.get("https://passport.bilibili.com/login")
        locator = (By.ID, "login-username")
        WebDriverWait(self.browser, 20).until(
            EC.presence_of_element_located(locator))
        self.send_key()
        position = self.get_position()
        self.get_geetest_image(position)
        self.shot_origin_screen()
        self.get_geetest_image(position, "img1.png")
        self.mouse_action()


if __name__ == "__main__":
    B = BilibiliLogin()
    B.login()
