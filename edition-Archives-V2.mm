<map version="0.9.0">
<!--To view this file, download free mind mapping software Freeplane from http://freeplane.sourceforge.net -->
<node TEXT="&#xc9;dition Archives V2" ID="ID_836777265" CREATED="1389984575237" MODIFIED="1389984584290">
<hook NAME="MapStyle" max_node_width="300"/>
<node TEXT="id&#xe9;es" POSITION="right" ID="ID_1753238244" CREATED="1389984585356" MODIFIED="1389984588697">
<node TEXT="passer en OO, au moins pour ce qui est des m&#xe9;dias" ID="ID_559861590" CREATED="1389984589396" MODIFIED="1389984610232"/>
<node TEXT="utiliser XML ou JSON pour passer les donn&#xe9;es de PHP &#xe0; JS et r&#xe9;ciproquement" ID="ID_1505710718" CREATED="1389984610716" MODIFIED="1389984638272"/>
<node TEXT="besoin d&apos;un design &quot;clean&quot; parce que sinon c&apos;est l&apos;usine &#xe0; gaz" ID="ID_1977765261" CREATED="1389984641747" MODIFIED="1389984666903"/>
<node TEXT="utiliser des URL pour sp&#xe9;cifier les cibles permettrait d&apos;unifier les approches et de simplifier la hi&#xe9;rarchie de classes ??" ID="ID_1462026137" CREATED="1390165959287" MODIFIED="1390165986635"/>
</node>
<node TEXT="Flow" POSITION="left" ID="ID_1698139078" CREATED="1389985859961" MODIFIED="1389985863245">
<node TEXT="l&apos;XML est lu par PHP, puis XSL qui cr&#xe9;e la page" ID="ID_421104897" CREATED="1389985868512" MODIFIED="1389985945428"/>
<node TEXT="les objets m&#xe9;dia sont cr&#xe9;&#xe9;s par le XSL via un appel au constructeur qui prend les donn&#xe9;es XML en param&#xe8;tres" ID="ID_973261444" CREATED="1389985947783" MODIFIED="1389985989699"/>
</node>
<node TEXT="classes" POSITION="right" ID="ID_164204486" CREATED="1389985998245" MODIFIED="1389986087212" HGAP="42" VSHIFT="68">
<node TEXT="media" ID="ID_489009631" CREATED="1389986014765" MODIFIED="1389993338408" HGAP="23" VSHIFT="-38">
<node TEXT="publique" ID="ID_1731601264" CREATED="1389993274987" MODIFIED="1389996754689">
<node TEXT="attributs" ID="ID_295666091" CREATED="1389986059428" MODIFIED="1389986077801">
<node TEXT="commentaire" ID="ID_1718659229" CREATED="1389992957178" MODIFIED="1390081593851">
<icon BUILTIN="button_ok"/>
</node>
<node TEXT="position dans la liste" ID="ID_852212957" CREATED="1389993174934" MODIFIED="1390081596271">
<icon BUILTIN="button_ok"/>
</node>
<node TEXT="id" ID="ID_882779054" CREATED="1389993448856" MODIFIED="1390081599047">
<icon BUILTIN="button_ok"/>
<node TEXT="permet retrouver &quot;ses affaires&quot; dans le DOM" ID="ID_1049207573" CREATED="1389993470143" MODIFIED="1389993488427"/>
</node>
</node>
<node TEXT="m&#xe9;thodes" ID="ID_204099724" CREATED="1389986078356" MODIFIED="1390164252428" HGAP="24" VSHIFT="33">
<node TEXT="constructeur(commentaire)" ID="ID_11752871" CREATED="1389993497183" MODIFIED="1390122997067">
<icon BUILTIN="button_ok"/>
</node>
<node TEXT="editerCommentaire" ID="ID_244372049" CREATED="1389993686835" MODIFIED="1390122999298">
<icon BUILTIN="button_ok"/>
</node>
<node TEXT="supprimer" ID="ID_198682808" CREATED="1389993720738" MODIFIED="1390123002666">
<icon BUILTIN="button_ok"/>
</node>
<node TEXT="bouger" ID="ID_1751826772" CREATED="1389993945021" MODIFIED="1389993947554">
<node TEXT="fonction virtuelle" ID="ID_130885430" CREATED="1389993948597" MODIFIED="1389993952929"/>
<node TEXT="le media devient d&#xe9;pla&#xe7;able par la mollette de la souris, puis positionnable par un clic gauche" ID="ID_1570913766" CREATED="1389994100602" MODIFIED="1389994140726"/>
</node>
<node TEXT="toXML" ID="ID_752759055" CREATED="1389993989508" MODIFIED="1389994062048">
<node TEXT="fonction virtuelle" ID="ID_956664243" CREATED="1389994022140" MODIFIED="1389994026336"/>
<node TEXT="donne la structure xml du media pour la BD" ID="ID_182855742" CREATED="1389994145841" MODIFIED="1389994170789"/>
</node>
</node>
</node>
<node TEXT="priv&#xe9;" ID="ID_532334671" CREATED="1389993277435" MODIFIED="1389994178742" HGAP="28" VSHIFT="31">
<node TEXT="attributs" ID="ID_628133945" CREATED="1389993862223" MODIFIED="1389993864803">
<node TEXT="vivant" ID="ID_1922267985" CREATED="1389994263358" MODIFIED="1390081602583">
<icon BUILTIN="button_ok"/>
<node TEXT="bool&#xe9;en" ID="ID_1414485047" CREATED="1389994267502" MODIFIED="1389994733873"/>
<node TEXT="vivant = doit &#xea;tre cr&#xe9;&#xe9; ou conserv&#xe9; apr&#xe8;s validation" ID="ID_891701163" CREATED="1389994279974" MODIFIED="1389994307218"/>
<node TEXT="mort = doit &#xea;tre oubli&#xe9; ou effac&#xe9; apr&#xe8;s validation" ID="ID_1090955180" CREATED="1389994311093" MODIFIED="1389994325954"/>
</node>
</node>
</node>
</node>
<node TEXT="media:fileMedia" ID="ID_1568347367" CREATED="1389995353439" MODIFIED="1389995666079" HGAP="43" VSHIFT="-39">
<node TEXT="publique" ID="ID_1386518657" CREATED="1389993296459" MODIFIED="1389996761166" HGAP="29">
<node TEXT="attributs" ID="ID_1550467795" CREATED="1389993035664" MODIFIED="1389993041917">
<node TEXT="fichier miniature (serveur)" ID="ID_1788813325" CREATED="1389993069808" MODIFIED="1390037491086"/>
<node TEXT="fichier image (serveur)" ID="ID_958548300" CREATED="1389993095527" MODIFIED="1390037494566"/>
</node>
<node TEXT="m&#xe9;thodes" ID="ID_187053151" CREATED="1389993042216" MODIFIED="1389996803190" HGAP="23" VSHIFT="18">
<node TEXT="constructeur(commentaire,fichier*, fichier miniature*)" ID="ID_1025067105" CREATED="1389994770020" MODIFIED="1390037454666"/>
<node TEXT="upload(fichierLocal)" ID="ID_254916744" CREATED="1390037402924" MODIFIED="1390037475143"/>
<node TEXT="supprimer" ID="ID_1550570622" CREATED="1389993875519" MODIFIED="1389993880099"/>
</node>
</node>
</node>
<node TEXT="media:fileMedia:photo" ID="ID_716746213" CREATED="1389986023197" MODIFIED="1390158400243" HGAP="84" VSHIFT="-29">
<node TEXT="publique" ID="ID_116769910" CREATED="1389993296459" MODIFIED="1390036209547" HGAP="48" VSHIFT="3">
<node TEXT="m&#xe9;thodes" ID="ID_1224862292" CREATED="1389993042216" MODIFIED="1389993045349">
<node TEXT="constructeur(commentaire,fichier image*, fichier miniature*)" ID="ID_1003059527" CREATED="1389994770020" MODIFIED="1390037520209"/>
<node TEXT="toXML" ID="ID_1740416415" CREATED="1389994034443" MODIFIED="1389994055271">
<node TEXT="&lt;photo fichier=&quot;...&quot; commentaire=&quot;...&quot; /&gt;" ID="ID_77506265" CREATED="1389995015782" MODIFIED="1389995216863"/>
</node>
</node>
</node>
</node>
<node TEXT="media:fileMedia:video" ID="ID_220443102" CREATED="1389986035085" MODIFIED="1389995679926" HGAP="81" VSHIFT="-31">
<node TEXT="publique" ID="ID_1340508092" CREATED="1389993310803" MODIFIED="1390036211619" HGAP="42" VSHIFT="4">
<node TEXT="m&#xe9;thodes" ID="ID_1000044599" CREATED="1389993042216" MODIFIED="1389993045349">
<node TEXT="constructeur(commentaire,fichier vid&#xe9;o*, fichier miniature*)" ID="ID_1704721914" CREATED="1389994868465" MODIFIED="1390037507993"/>
<node TEXT="choixMiniature" ID="ID_297342652" CREATED="1389995702464" MODIFIED="1389995711428">
<node TEXT="permet de choisir une autre miniature que celle par d&#xe9;faut" ID="ID_1019909314" CREATED="1389995720039" MODIFIED="1389995731628"/>
</node>
<node TEXT="toXML" ID="ID_1916575962" CREATED="1389994037443" MODIFIED="1389994050591">
<node TEXT="&lt;video fichier=&quot;...&quot; commentaire=&quot;...&quot; /&gt;" ID="ID_283005150" CREATED="1389995015782" MODIFIED="1389995194647"/>
</node>
</node>
</node>
</node>
<node TEXT="media:vimeo" ID="ID_1765828917" CREATED="1389986038725" MODIFIED="1389993347208" HGAP="37" VSHIFT="12">
<node TEXT="publique" ID="ID_836837631" CREATED="1389993325786" MODIFIED="1390036220457" HGAP="51" VSHIFT="-1">
<node TEXT="attributs" ID="ID_1076522059" CREATED="1389993035664" MODIFIED="1389993041917">
<node TEXT="id vimeo" ID="ID_1266773751" CREATED="1389993147166" MODIFIED="1389993154651"/>
</node>
<node TEXT="m&#xe9;thodes" ID="ID_1861783077" CREATED="1389993042216" MODIFIED="1389993045349">
<node TEXT="constructeur(commentaire,id vimeo)" ID="ID_261093" CREATED="1389994891593" MODIFIED="1389994941956"/>
<node TEXT="supprimer" ID="ID_237259709" CREATED="1389993888286" MODIFIED="1389993890107"/>
<node TEXT="toXML" ID="ID_1306499922" CREATED="1389994039739" MODIFIED="1389994044376">
<node TEXT="&lt;vimeo id=&quot;...&quot; commentaire=&quot;...&quot; /&gt;" ID="ID_959815915" CREATED="1389995235058" MODIFIED="1389995253798"/>
</node>
</node>
</node>
</node>
</node>
</node>
</map>
