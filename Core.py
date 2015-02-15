'''
    Http remote plugin for XBMC
    Copyright (C) 2014 Mohell
	mshipitko@gmail.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
'''

import Localization
import sys
import xbmc
import xbmcaddon
import xbmcgui
import xbmcplugin
import urllib
import urllib2
import cookielib
import re
import tempfile
import urlparse



from BaseHTTPServer import BaseHTTPRequestHandler,HTTPServer
import threading
inst=None

PORT_NUMBER = 9900


#This class will handles any incoming request from
#the browser 
class myHandler(BaseHTTPRequestHandler):
	def do_GET(self):
		self.send_response(200)
		self.send_header('Content-type','text/html')
		self.end_headers()
		
		parsedParams = urlparse.urlparse(self.path)
		queryParsed = urlparse.parse_qs(parsedParams.query)
		inst.playMovie(queryParsed['url'][0],queryParsed['capt'][0])
		return



class Monitor(threading.Thread):
	def __init__(self):
		threading.Thread.__init__(self)
	
	def run(self):
		#Create a web server and define the handler to manage the
		#incoming request
		Monitor.server = HTTPServer(('', PORT_NUMBER), myHandler)
		print 'Started httpserver on port ' , PORT_NUMBER
		
		#Wait forever for incoming htto requests
		Monitor.server.serve_forever()




class Core:
	__plugin__ = sys.modules[ "__main__"].__plugin__
	__settings__ = sys.modules[ "__main__" ].__settings__
	ROWCOUNT = (15, 30, 50, 100)[int(__settings__.getSetting("rowcount"))]
	LANGUAGE = ('ru', 'uk', 'en')[int(__settings__.getSetting("language"))]
	ROOT = sys.modules[ "__main__"].__root__
	
	skinOptimizations = (
		{#Confluence
			'list': 50,
			'info': 50,
			'icons': 500,
		},
		{#Transperency!
			'list': 50,
			'info': 51,
			'icons': 53,
		}
	)
	
	
	# Private and system methods
	def __init__(self, localization):
		global inst
		inst=self
		self.localization = localization
		a = Monitor()	
		a.start()
	
	def playMovie(self, url,caption):
		
		#self.drawItem(caption, 'clear', image=self.ROOT + '/icons/search.png')
		#listitem = xbmcgui.ListItem(caption, iconImage=self.ROOT + '/icons/video.png', thumbnailImage=self.ROOT + '/icons/video.png')
		#xbmcplugin.addDirectoryItem(handle=int(sys.argv[1]), url=url, listitem=listitem, isFolder=False)
		
		#xbmc.executebuiltin("Action(Stop)")
		resultPlaylist = xbmc.PlayList(xbmc.PLAYLIST_VIDEO)
		#resultPlaylist.clear()
		image = self.ROOT + '/icons/video.png'
		listitem = xbmcgui.ListItem(caption, iconImage=image, thumbnailImage=image)
		resultPlaylist.add(url, listitem)
		#player = xbmc.Player(xbmc.PLAYER_CORE_AUTO)
		#player.play(resultPlaylist)
		xbmc.executebuiltin("ActivateWindow(VideoPlaylist)")	
	
	
	# Executable actions methods
	def executeAction(self, params = {}):
		get = params.get
		if hasattr(self, get("action")):
			getattr(self, get("action"))(params)
		else:
			self.sectionMenu()
	
	def drawItem(self, title, action, link = '', image=ROOT + '/icons/video.png', isFolder = True, contextMenu=None):
		listitem = xbmcgui.ListItem(title, iconImage=image, thumbnailImage=image)
		url = '%s?action=%s&url=%s' % (sys.argv[0], action, urllib.quote_plus(link))
		if contextMenu:
			listitem.addContextMenuItems(contextMenu)
		if isFolder:
			listitem.setProperty("Folder", "true")
		else:
			listitem.setInfo(type = 'Video', infoLabels = {"Title":title})
		xbmcplugin.addDirectoryItem(handle=int(sys.argv[1]), url=url, listitem=listitem, isFolder=isFolder)

			
	def getParameters(self, parameterString):
		commands = {}
		splitCommands = parameterString[parameterString.find('?')+1:].split('&')
		for command in splitCommands: 
			if (len(command) > 0):
				splitCommand = command.split('=')
				name = splitCommand[0]
				value = ''
				if len(splitCommand) == 2:
					value = splitCommand[1]
				commands[name] = value
		return commands
	
	
	def clear(self, params = {}):
		resultPlaylist = xbmc.PlayList(xbmc.PLAYLIST_VIDEO)
		resultPlaylist.clear()
		xbmcplugin.endOfDirectory(handle=int(sys.argv[1]), succeeded=False)
	
	def goToPl(self, params = {}):
		xbmc.executebuiltin("ActivateWindow(VideoPlaylist)")
		xbmcplugin.endOfDirectory(handle=int(sys.argv[1]), succeeded=False)
	
	
	def sectionMenu(self):
		self.drawItem("playlist", 'goToPl', image=self.ROOT + '/icons/search.png')
		self.drawItem("clear play list", 'clear', image=self.ROOT + '/icons/search.png')
		xbmcplugin.endOfDirectory(handle=int(sys.argv[1]), succeeded=True)
		

	
