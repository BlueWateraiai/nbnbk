
PDF����Ԥ��

1��file�����ǱȽϼ򵥵ķ�ʽ,ֻҪ֪�����ļ��������;����ˡ��������ַ�ʽ������Ŀ�еĻ�ֻ�ܴ���Ŀ���pdf�ļ������仰˵����PDF.jsĬ���ǲ��ܴ���Ŀ���ļ�ϵͳ���ļ���

���pdf�ļ���viewer.html����һ��Ŀ¼�У��ĳ����·�����ɡ�
ʾ����
/pdfjs/web/viewer.html?file=./compressed.tracemonkey-pldi-09.pdf

2���ļ�����ʽʵ������չʾpdf�ļ�

1)
�޸�viewer.js
var DEFAULT_URL = 'compressed.tracemonkey-pldi-09.pdf'  ������PDF��·��
ɾ���ñ�������

2)
ͨ��ajax�ķ�ʽ��ȡ�ļ������ݣ�������

-------------------------------------------------------------
JS����
var DEFAULT_URL = "";//ע�⣬ɾ���ı������������¶���
var PDFData = "";
$.ajax({
    type:"post",
    async:false,
    dataType:"json",
    url:�ļ��������ַ,
    success:function(data){
       var pdfData = atob(data);
    }
});
var rawLength = PDFData.length;
//ת����pdf.js��ֱ�ӽ�����Uint8Array����,��pdf.js-4068
var array = new Uint8Array(new ArrayBuffer(rawLength));
for(i = 0; i < rawLength; i++) {
  array[i] = PDFData.charCodeAt(i) & 0xff;
}
DEFAULT_URL = array;
-------------------------------------------------------------

������Ĵ�����Ϊjs�ļ����Ǵ���Ƭ�Σ�����viewer.html������Ĵ���Ҫ����<script src="viewer.js"></script>����֮ǰ����

-------------------------------------------------------------
���PHP����
$str = file_get_contents($_REQUEST['url']);
exit(json_encode(chunk_split(base64_encode($str))));
-------------------------------------------------------------

