package com.cpdms.model.auto;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

public class TsysDatas {
	@JsonSerialize(using= StringFormat.class)
    private String id;
	@JsonSerialize(using= StringFormat.class)
    private String filePath;

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id == null ? null : id.trim();
    }

    public String getFilePath() {
        return filePath;
    }

    public void setFilePath(String filePath) {
        this.filePath = filePath == null ? null : filePath.trim();
    }
}