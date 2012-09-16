<?php

require_once "../phpbem.php";

// use bem\bemhtml;

// Path with pages dirs
$wwwpath = __DIR__ . 'www';

// Create BEM renderer
$renderer = new bemhtml($wwwpath, $cache_context = true, $use_exts = false);


class MainHandler(tornado.web.RequestHandler):
    def get(self):
        // Create some dynamic context
        context = {'url' : 'http://example.com/',
                   'text': 'Привет',
                   'title': lambda title: '(%s) %s' % (self.request.uri, title)}# (path. request.uri)}
        env = self.request
        # Render page example from pages
        # Calls BEMHTML.apply(render(context, env))
        message = renderer.render('pages/example', context, env, "render",
                                  extra_files=['example.en.js'])
        self.write(message)

/**
if __name__ == "__main__":
    application = tornado.web.Application([
        (r"/", MainHandler),
        (r"/static/(.*)", tornado.web.StaticFileHandler, {"path": os.path.join(wwwpath, 'pages')}),
        (r"/blocks/(.*)", tornado.web.StaticFileHandler, {"path": os.path.join(wwwpath, 'blocks')}),
        (r"/bem-bl/(.*)", tornado.web.StaticFileHandler, {"path": os.path.join(wwwpath, 'bem-bl')}),
    ])
    application.listen(3000)
    tornado.ioloop.IOLoop.instance().start()
*/
