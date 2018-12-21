<!doctype html>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<script type='text/javascript'>
		var active_page = "catalog"
		  , board_name = "{{ board }}";
	</script>
	{% include 'header.html' %}
	<title>{{ board }} - Catalog</title>
</head>
<body class="8chan vichan {% if mod %}is-moderator{% else %}is-not-moderator{% endif %} theme-catalog active-catalog" data-stylesheet="{% if config.default_stylesheet.1 != '' %}{{ config.default_stylesheet.1 }}{% else %}default{% endif %}">

	{{ boardlist.top }}
{% include 'checkban.php' %}
	
	<header>
		<h1>{{ settings.title }} (<a href="{{link}}">/{{ board }}/</a>)</h1>
		<div class="subtitle">{{ settings.subtitle }}</div>
	</header>

        <span>{% trans 'Sort by' %}: </span>
        <select id="sort_by" style="display: inline-block">
                <option selected value="bump:desc">{% trans 'Bump order' %}</option>
                <option value="time:desc">{% trans 'Creation date' %}</option>
                <option value="reply:desc">{% trans 'Reply count' %}</option>
                <option value="random:desc">{% trans 'Random' %}</option>
        </select>
 
        <span>{% trans 'Image size' %}: </span>
        <select id="image_size" style="display: inline-block">
                <option value="vsmall">{% trans 'Very small' %}</option>
                <option selected value="small">{% trans 'Small' %}</option>
                <option value="large">{% trans 'Large' %}</option>
        </select>
        <div class="threads">
                <div id="Grid">
                {% for post in recent_posts %}
                        <div class="mix"
				data-reply="{{ post.reply_count }}"
				 data-bump="{{ post.bump }}"
				 data-time="{{ post.time }}"
				 data-id="{{ post.id }}"
				 data-sticky="{% if post.sticky %}true{% else %}false{% endif %}"
				 data-locked="{% if post.locked %}true{% else %}false{% endif %}"
			>
                                <div class="thread grid-li grid-size-small">  
                                        <a href="{{post.link}}">  
						{% if post.youtube %}
							<img src="//img.youtube.com/vi/{{ post.youtube }}/0.jpg" 
						{% else %}
							<img src="{{post.file}}" 
						{% endif %}
                                                 id="img-{{ post.id }}" data-subject="{% if post.subject %}{{ post.subject|e }}{% endif %}" data-name="{{ post.name|e }}" data-muhdifference="{{ post.muhdifference }}" class="{{post.board}} thread-image" title="{{post.bump|date('%b %d %H:%M')}}">
                                        </a>
                                                <div class="replies">
                                                        <strong>R: {{ post.reply_count }} / I: {{ post.image_count }}{% if post.sticky %} (sticky){% endif %}</strong>
                                                        {% if post.subject %}
								<p class="intro">
									<span class="subject">
										{{ post.subject|e }}
									</span>
								</p>
							{% else %}
								<br />
							{% endif %}

								{{ post.body }}
                                                </div>
                                </div>
                        </div>
                {% endfor %}
                </div>
        </div>
	<hr/>
	<footer>
		<p class="unimportant" style="margin-top:20px;text-align:center;">- Tinyboard + 
			<a href="https://engine.vichan.net/">vichan</a> {{ config.version }} -
		<br>Tinyboard Copyright &copy; 2010-2014 Tinyboard Development Group    
		<br><a href="https://engine.vichan.net/">vichan</a> Copyright &copy; 2012-2018 vichan-devel</p>
	</footer>
	<!-- <script type="text/javascript">{% raw %} -->
		<!-- var styles = { -->
			<!-- {% endraw %} -->
			<!-- {% for name, uri in config.stylesheets %}{% raw %}'{% endraw %}{{ name|addslashes }}{% raw %}' : '{% endraw %}/stylesheets/{{ uri|addslashes }}{% raw %}', -->
			<!-- {% endraw %}{% endfor %}{% raw %} -->
		<!-- };  -->
		<!-- if(is_style_select == false){ -->
			<!-- onready(init); -->
		<!-- } -->
	<!-- {% endraw %}</script> -->

	<script type="text/javascript">{% raw %}
		ready();
	{% endraw %}</script>
</body>
</html>
