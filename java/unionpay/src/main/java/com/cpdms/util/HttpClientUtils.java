package com.cpdms.util;


import org.springframework.http.*;
import org.springframework.util.LinkedMultiValueMap;
import org.springframework.util.MultiValueMap;
import org.springframework.web.client.RestTemplate;


public class HttpClientUtils {

	public static String sendJsonPostRequest(String url, MultiValueMap<String, String> params) {
	    RestTemplate restTemplate = new RestTemplate();
		HttpMethod method = HttpMethod.POST;
		HttpHeaders headers = new HttpHeaders();
		headers.setContentType(MediaType.APPLICATION_JSON_UTF8);
		HttpEntity<MultiValueMap<String, String>> requestEntity = new HttpEntity<MultiValueMap<String, String>>(params, headers);

		ResponseEntity<String> response = restTemplate.exchange(url, method, requestEntity, String.class);

		return response.getBody();
	}

	public static String sendJsonGetRequest(String url, MultiValueMap<String, String> params) {
	    RestTemplate restTemplate = new RestTemplate();
	    HttpHeaders headers = new HttpHeaders();
		headers.setContentType(MediaType.APPLICATION_JSON_UTF8);
	    HttpEntity entity = new HttpEntity(headers);
	    HttpEntity<String> response = restTemplate.exchange(url, HttpMethod.GET, entity, String.class, params);
	    System.out.println(response);
	    return response.getBody();
    }

    public static void main(String[] args) {
	    String url = "http://t.weather.sojson.com/api/weather/city/101030100";
	    MultiValueMap<String,String> params = new LinkedMultiValueMap<String,String>();
	    System.out.println(HttpClientUtils.sendJsonGetRequest(url, params));
    }

}
