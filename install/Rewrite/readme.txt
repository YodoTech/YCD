��ѹ�ļ���C�̸�Ŀ¼��

��Ҫ����Rewrite��IISվ���Isapi��������ɸѡ��

ɸѡ������Rewrite

��ִ���ļ�ѡ�� c:\Rewrite\Rewrite.dll����������

httpd.ini�������ļ�
�����Ҫ��վ�������������������´���ӵ�httpd.ini���档
RewriteCond Host: (.+)

RewriteCond Referer: (?!http://\\1.*).*

RewriteRule .*\.(?:gif|jpg|png|) /block.gif [I,O]

����Ĵ�����˼�Ƕ�վ����з���������

�����Ҫ��ĳһ������ĳ����վ�㲻���з��������޸ģ�RewriteCond Referer: (?!http://\\1.*).* ���
�����޸�Ϊ
RewriteCond Referer: (?!http://(?:u\.discuz\.net|www\.discuz\.net)).+

��������������˼���ǳ���http://u.discuz.net�Լ�www.discuz.net������վ�㣬��������վ�ϵ���ȫ���ܾ���

Ȼ������վ��Ŀ¼�½���block.gif�ļ� 

��������վ��ʾ�ľ������ͼƬ��

