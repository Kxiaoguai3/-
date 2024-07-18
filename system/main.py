import const
import function

while True:
    function.Print_Menu()

    choose = int(input("请输入对应功能的序号："))
    #添加
    if choose == 1:
       function.Add_Student()
    #删除
    elif choose == 2:
        function.Delete_Student()

    #修改
    elif choose == 3:
        print("该功能异常，请谨慎使用")
        function.Modify_Student()

    #查找
    elif choose == 4:
        function.Find_Stduenr()
    #列出
    elif choose == 5:
        function.List_Student()
    #退出
    elif choose == 6:
        exit()
    #其他
    else:
        print("无效指令")


    input('按回车键继续')
