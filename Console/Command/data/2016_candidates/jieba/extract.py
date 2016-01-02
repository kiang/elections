# encoding=utf-8
import jieba, sys
reload(sys)
sys.setdefaultencoding('utf-8')

inputString = ''
argIndex = 0;
for arg in sys.argv:
    if argIndex > 0:
        inputString += arg + ' '
    argIndex+=1

seg_list = jieba.cut(inputString, cut_all=False)
print("\t".join(seg_list))