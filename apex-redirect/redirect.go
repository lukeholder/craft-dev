package main

import (
	"log"
	"net/http"
	"net/url"
)

func main() {
	http.HandleFunc("/", redirect)
	listenAddr := "0.0.0.0:8080"
	log.Println("Listing on:", listenAddr)
	log.Fatalln(http.ListenAndServe(listenAddr, nil))
}

func redirect(w http.ResponseWriter, r *http.Request) {
	log.Println("Request URL:", r.URL.String())
	redirUrl := url.URL{
		Scheme:      "https",
		Host:        "www.teentix.org",
		Path:        r.URL.Path,
		RawPath:     r.URL.RawPath,
		ForceQuery:  r.URL.ForceQuery,
		RawQuery:    r.URL.RawQuery,
		Fragment:    r.URL.Fragment,
		RawFragment: r.URL.RawFragment,
	}
	redirUrlStr := redirUrl.String()
	log.Println("Redirecting to:", redirUrlStr)
	http.Redirect(w, r, redirUrlStr, 301)
}
