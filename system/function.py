from const import DATA
import const

#一个没什么用的类
class File_Manager:
    def __init__(self, name, age, grade, Class, grades):
        self.name = name
        self.age = age
        self.grade = grade
        self.Class = Class
        self.grades = grades

    def Input(self):
        with open(DATA, 'a') as file:
            file.write(f'姓名: {self.name}, 年龄: {self.age}, 年级: {self.grade}, 班级: {self.Class}, 成绩: {self.grades}\n')
#打印目录
def Print_Menu():
    print("欢迎使用学生管理系统  ", const.VERSION)
    print("--------------------")
    print("1.添加学生")
    print("2.删除学生")
    print("3.修改学生")
    print("4.查询学生")
    print("5.显示所有学生")
    print("6.退出系统")
    print("--------------------")
#添加
def Add_Student():
    name = input("请输入学生姓名：")
    age = input("请输入学生年龄：")
    grade = input("请输入学生年级：")
    Class = input("请输入学生班级：")
    grades = input("请输入学生成绩")
    tmp = File_Manager(name, age, grade, Class, grades)
    tmp.Input()
    print(f"{name} 已添加成功")
#列出
def List_Student():
        with open(DATA, 'r') as file:
            content = file.read()
            print(content) 
#删除
def Delete_Student():
    name = input("请输入要删除学生的姓名：")

    with open(DATA, 'r') as file:
        lines = file.readlines()

    new_line = []
    is_Found = False

    for line in lines:
        if name in line:
            is_Found = True
        else:
            new_line.append(line)
    if is_Found:
        with open(DATA, 'w') as file:
            file.writelines(new_line)
        print(f"{name}的信息已删除成功")
    else:
        print(f"未找到{name}的信息")


#修改
def Modify_Student():
    with open(DATA, 'r') as file:
        liens = file.readlines()
    
    name = input('请输入你要修改学生的姓名')
    new_lines = []

    for line in liens:
            is_Found = True
            fields = line.split(', ')
            
            if name in line:
                is_Found = True
                print('1.姓名')
                print('2.年龄')
                print('3.年级')
                print('4.班级')
                print('5.成绩')
                choose = int(input("请输入要修改信息的序号："))
            
                
                if choose == 1:
                    new_info = input("输入新姓名: ")
                    fields[0] = "姓名: " + new_info
                if choose == 2:
                    new_info = input("输入新年龄: ")
                    fields[1] = "年龄: " + new_info
                if choose == 3:
                    new_info = input("输入新年级: ")
                    fields[2] = "年级: " + new_info
                if choose == 4:
                    new_info = input("输入新班级: ")
                    fields[3] = "班级: " + new_info
                if choose == 5:
                    new_info = input("输入新成绩: ")
                    fields[4] = "成绩: " + new_info


                new_lines.append(', '.join(fields) + '\n')
                print(f'{name}的信息修改成功')

            else:
                new_lines.append(line)

    if is_Found:
        with open(DATA, 'w') as file:
            file.writelines(new_lines)
            print("学生信息修改成功！")
    else:
        print("未找到该学生信息。")

#查找
def Find_Stduenr():
    name = input("请示如学生姓名：")
    with open(DATA, 'r') as file:
        lines = file.readlines()
    for line in lines:
        if name in line:
            print(line)
            return
    print(f"未能找到{name}")
